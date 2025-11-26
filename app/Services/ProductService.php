<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProductService
{
    // Các key không lưu trực tiếp vào bảng products mà cần xử lý riêng
    protected const NON_DB_KEYS = ['gallery', 'category_ids', 'specs', 'specifications', 'stock_locations', 'stock_locations_input', 'image'];

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
            // 1. Chuẩn bị dữ liệu
            $fillableData = $this->prepareData($data);

            // 2. Xử lý ảnh đại diện
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $fillableData['image'] = $data['image']->store('products', 'public');
            }

            // 3. Tạo Product
            $product = Product::create($fillableData);

            // 4. Xử lý Gallery
            $this->handleGalleryUpload($product, $data['gallery'] ?? null);

            return $product;
        });
    }

    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            // 1. Chuẩn bị dữ liệu
            $fillableData = $this->prepareData($data, $product);

            // 2. Xử lý ảnh đại diện (Xóa cũ, thêm mới)
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $fillableData['image'] = $data['image']->store('products', 'public');
            }

            // 3. Cập nhật Product
            $product->update($fillableData);

            // 4. Xử lý Gallery (Thêm mới)
            $this->handleGalleryUpload($product, $data['gallery'] ?? null);

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

            // Ảnh đầu tiên trong gallery sẽ làm ảnh đại diện (Logic tùy chọn)
            $firstImage = $product->images()->orderBy('sort_order', 'asc')->first();
            if ($firstImage) {
                $product->update(['image' => $firstImage->path]);
            }

            return true;
        });
    }

    public function countLowStock(): int
    {
        return Product::whereColumn('quantity', '<=', 'min_stock')->count();
    }

    public function getAllIds(array $filters): array
    {
        return Product::pluck('id')->toArray(); // Có thể apply filter vào đây nếu cần chính xác hơn
    }

    public function findActiveBySlug(string $slug)
    {
        return Product::where('slug', $slug)
            ->where('status', 'active')
            ->with(['category', 'images' => function ($q) {
                $q->orderBy('sort_order');
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
        // Auto generate slug & SKU
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);
        }
        if (empty($data['sku']) && !$product) { // Chỉ tạo SKU mới khi create
            $data['sku'] = 'SKU-' . strtoupper(Str::random(8));
        }

        // Xử lý JSON Metadata (Specs & Locations)
        $metadata = $product ? ($product->metadata ?? []) : [];

        if (isset($data['specifications'])) {
            $metadata['specs'] = is_string($data['specifications'])
                ? json_decode($data['specifications'], true)
                : $data['specifications'];
        }

        if (isset($data['stock_locations'])) {
            $data['stock_locations'] = is_string($data['stock_locations'])
                ? json_decode($data['stock_locations'], true)
                : $data['stock_locations'];
            // Lưu stock_locations vào metadata hoặc cột riêng tùy DB, giả sử vào metadata
            $metadata['stock_locations'] = $data['stock_locations'];
        }

        $data['metadata'] = $metadata;

        // Lọc bỏ các key không phải cột DB
        return array_diff_key($data, array_flip(self::NON_DB_KEYS));
    }

    private function handleGalleryUpload(Product $product, $galleryFiles)
    {
        if (!empty($galleryFiles) && is_array($galleryFiles)) {
            foreach ($galleryFiles as $file) {
                if ($file instanceof UploadedFile) {
                    $path = $file->store('products/gallery', 'public');
                    $product->images()->create(['path' => $path]);
                }
            }
        }
    }
}
