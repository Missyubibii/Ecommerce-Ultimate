<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProductService
{
    // Các key không lưu trực tiếp vào bảng products mà cần xử lý riêng
    protected const NON_DB_KEYS = [
        'gallery',
        'category_ids',
        'specs',
        'specifications',
        'stock_locations',
        'stock_locations_input',
        'image',
        'image_colors',
        'images_data',      // JSON cấu trúc ảnh
        'deleted_image_ids' // Array ID ảnh xóa
    ];

    // --- ADMIN METHODS ---

    public function listing(array $filters, int $perPage = 10)
    {
        $query = Product::with('category');

        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['q']}%")
                    ->orWhere('sku', 'like', "%{$filters['q']}%");
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $sort = $filters['sort'] ?? 'created_at';
        $direction = $filters['direction'] ?? 'desc';

        if (in_array($sort, ['price', 'name', 'created_at', 'quantity'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $fillableData = $this->prepareData($data);

            // LOGIC CREATE: Lấy file đầu làm avatar
            $galleryFiles = $data['gallery'] ?? [];

            if (!empty($galleryFiles) && is_array($galleryFiles) && count($galleryFiles) > 0) {
                if ($galleryFiles[0] instanceof UploadedFile) {
                    $fillableData['image'] = $galleryFiles[0]->store('products', 'public');
                }
            }

            $product = Product::create($fillableData);

            $this->handleGalleryUpload($product, $galleryFiles);

            return $product;
        });
    }

    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $fillableData = $this->prepareData($data, $product);

            $product->update($fillableData);

            // 1. Xóa các ảnh đã bị user remove khỏi UI
            if (!empty($data['deleted_image_ids']) && is_array($data['deleted_image_ids'])) {
                $imagesToDelete = ProductImage::whereIn('id', $data['deleted_image_ids'])
                    ->where('product_id', $product->id)
                    ->get();
                foreach ($imagesToDelete as $img) {
                    Storage::disk('public')->delete($img->path);
                    $img->delete();
                }
            }

            // 2. Xử lý Sắp xếp & Upload mới dựa trên 'images_data'
            if (!empty($data['images_data'])) {
                $imagesStructure = json_decode($data['images_data'], true);
                $newFiles = $data['gallery'] ?? [];

                if (is_array($imagesStructure)) {
                    foreach ($imagesStructure as $index => $item) {
                        $color = ($item['color'] === '' || $item['color'] === 'null') ? null : $item['color'];

                        // Ảnh cũ
                        if (!empty($item['id'])) {
                            ProductImage::where('id', $item['id'])
                                ->where('product_id', $product->id)
                                ->update([
                                    'sort_order' => $index,
                                    'color' => $color
                                ]);
                        }
                        // Ảnh mới
                        else {
                            $fileIndex = $item['new_file_index'] ?? -1;
                            if (isset($newFiles[$fileIndex]) && $newFiles[$fileIndex] instanceof UploadedFile) {
                                $path = $newFiles[$fileIndex]->store('products/gallery', 'public');
                                $product->images()->create([
                                    'path' => $path,
                                    'sort_order' => $index,
                                    'color' => $color
                                ]);
                            }
                        }
                    }
                }
            }

            // 3. Đồng bộ lại Avatar
            $firstImage = $product->images()->orderBy('sort_order', 'asc')->first();
            if ($firstImage) {
                $product->update(['image' => $firstImage->path]);
            } else {
                $product->update(['image' => null]);
            }

            return $product;
        });
    }

    public function delete(Product $product): bool
    {
        if ($product->image) Storage::disk('public')->delete($product->image);
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        return $product->delete();
    }

    public function reorderImages(Product $product, array $imageIds)
    {
        return DB::transaction(function () use ($product, $imageIds) {
            foreach ($imageIds as $index => $id) {
                $product->images()->where('id', $id)->update(['sort_order' => $index]);
            }
            // Sync avatar
            $firstImage = $product->images()->orderBy('sort_order', 'asc')->first();
            if ($firstImage) $product->update(['image' => $firstImage->path]);

            return true;
        });
    }

    public function countLowStock(): int
    {
        return Product::whereColumn('quantity', '<=', 'min_stock')->count();
    }

    public function getAllIds(array $filters): array
    {
        return Product::pluck('id')->toArray();
    }

    // --- FRONTEND / PUBLIC METHODS ---

    /**
     * Lấy danh sách sản phẩm gợi ý bán chạy nhất (Active only)
     * Dùng cho trang Search khi không có kết quả hoặc Homepage
     */
    public function getBestSellingSuggestions(int $limit = 4)
    {
        // Kiểm tra xem cột sold_count có tồn tại không để tránh lỗi SQL
        $sortColumn = \Illuminate\Support\Facades\Schema::hasColumn('products', 'sold_count') ? 'sold_count' : 'created_at';

        return Product::query()
            ->with(['category', 'product_images']) // Eager load để tránh lỗi N+1 query
            ->where('status', 'active')            // Chỉ lấy sản phẩm đang bán
            ->orderBy($sortColumn, 'desc')         // Sắp xếp giảm dần
            ->take($limit)                       
            ->get();
    }

    public function findActiveBySlug(string $slug)
    {
        return Product::where('slug', $slug)
            ->where('status', 'active')
            ->with(['category', 'images' => function ($q) {
                $q->orderBy('sort_order', 'asc');
            }])
            ->firstOrFail();
    }

    public function getProductsByCategory(string $categorySlug, int $perPage = 12, array $filters = [])
    {
        // 1. Tìm danh mục cha
        $category = Category::where('slug', $categorySlug)->firstOrFail();

        // 2. Lấy tất cả ID danh mục con (để filter sâu)
        $categoryIds = $category->children()->pluck('id')->push($category->id);

        // 3. Query sản phẩm
        $query = Product::whereIn('category_id', $categoryIds)
            ->where('status', 'active');

        // Filter: Giá
        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }
        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        // Sort
        if (isset($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                default:
                    $query->latest(); // newset
            }
        } else {
            $query->latest();
        }

        return [
            'category' => $category,
            'products' => $query->paginate($perPage)->withQueryString()
        ];
    }

    // --- Private Helpers ---

    private function prepareData(array $data, ?Product $product = null): array
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);
        }
        if (empty($data['sku']) && !$product) {
            $data['sku'] = 'SKU-' . strtoupper(Str::random(8));
        }

        $metadata = $product ? ($product->metadata ?? []) : [];

        if (isset($data['specifications'])) {
            $metadata['specs'] = is_string($data['specifications']) ? json_decode($data['specifications'], true) : $data['specifications'];
        }

        $data['metadata'] = $metadata;

        if (isset($data['colors'])) {
            $data['colors'] = is_string($data['colors']) ? json_decode($data['colors'], true) : $data['colors'];
        }

        return array_diff_key($data, array_flip(self::NON_DB_KEYS));
    }

    private function handleGalleryUpload(Product $product, $galleryFiles)
    {
        if (!empty($galleryFiles) && is_array($galleryFiles)) {
            $currentMaxSort = $product->images()->max('sort_order') ?? -1;
            foreach ($galleryFiles as $index => $file) {
                if ($file instanceof UploadedFile) {
                    $path = $file->store('products/gallery', 'public');
                    $product->images()->create([
                        'path' => $path,
                        'sort_order' => $currentMaxSort + 1 + $index
                    ]);
                }
            }
        }
    }
}
