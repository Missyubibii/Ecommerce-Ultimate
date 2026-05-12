<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProductService
{
    // Các key không lưu trực tiếp vào bảng products mà cần xử lý riêng thông qua Metadata hoặc quan hệ
    protected const NON_DB_KEYS = [
        'gallery',
        'category_ids',
        'specs',
        'specifications',
        'stock_locations',
        'stock_locations_input',
        'image',
        'image_colors',
        'images_data',      // JSON cấu trúc ảnh (dùng cho update)
        'deleted_image_ids', // Mảng ID ảnh cần xóa
        'new_brand_logo'     // Logo thương hiệu mới thêm nhanh
    ];

    // =========================================================================
    // SECTION 1: ADMIN METHODS (Dành cho trang quản trị)
    // =========================================================================

    /**
     * 1. Lấy danh sách sản phẩm có phân trang và bộ lọc
     */
    public function listing(array $filters, int $perPage = 10)
    {
        // Khởi tạo query cùng với danh mục (Eager Loading)
        $query = Product::with('category');

        // Lọc theo từ khóa (Tên sản phẩm hoặc SKU)
        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['q']}%")
                    ->orWhere('sku', 'like', "%{$filters['q']}%");
            });
        }

        // Lọc theo danh mục cụ thể
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Lọc theo trạng thái (active/inactive)
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Xử lý sắp xếp dữ liệu
        $sort = $filters['sort'] ?? 'price'; //price, name, created_at, quantity
        $direction = $filters['direction'] ?? 'desc'; //asc, desc

        if (in_array($sort, ['price', 'name', 'created_at', 'quantity'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
            //$query->orderBy('price', 'asc');
        }

        return $query->paginate($perPage);
    }

    /**
     * 2. Tạo sản phẩm mới
     */
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // Bước A: Chuẩn bị dữ liệu sạch (loại bỏ các key không có trong DB)
            $fillableData = $this->prepareData($data);

            // Bước B: Xử lý ảnh đại diện (Lấy file đầu tiên trong gallery làm avatar)
            $galleryFiles = $data['gallery'] ?? [];
            if (!empty($galleryFiles) && is_array($galleryFiles) && count($galleryFiles) > 0) {
                if ($galleryFiles[0] instanceof UploadedFile) {
                    $fillableData['image'] = $galleryFiles[0]->store('products', 'public');
                }
            }

            // Bước C: Lưu thông tin vào database
            $product = Product::create($fillableData);

            // Bước D: Upload toàn bộ ảnh vào bộ sưu tập (Gallery)
            $this->handleGalleryUpload($product, $galleryFiles);

            return $product;
        });
    }

    /**
     * 3. Cập nhật thông tin sản phẩm
     */
    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            // Bước A: Chuẩn bị dữ liệu và cập nhật bảng chính
            $fillableData = $this->prepareData($data, $product);
            $product->update($fillableData);

            // Bước B: Xóa các ảnh cũ nếu user nhấn nút xóa trên giao diện
            if (!empty($data['deleted_image_ids']) && is_array($data['deleted_image_ids'])) {
                $imagesToDelete = ProductImage::whereIn('id', $data['deleted_image_ids'])
                    ->where('product_id', $product->id)
                    ->get();
                foreach ($imagesToDelete as $img) {
                    Storage::disk('public')->delete($img->path);
                    $img->delete();
                }
            }

            // Bước C: Xử lý cấu trúc ảnh (Sắp xếp lại thứ tự và upload ảnh mới thêm)
            if (!empty($data['images_data'])) {
                $imagesStructure = json_decode($data['images_data'], true);
                $newFiles = $data['gallery'] ?? [];

                if (is_array($imagesStructure)) {
                    foreach ($imagesStructure as $index => $item) {
                        $color = ($item['color'] === '' || $item['color'] === 'null') ? null : $item['color'];

                        // Nếu là ảnh cũ: Cập nhật thứ tự sắp xếp và màu sắc
                        if (!empty($item['id'])) {
                            ProductImage::where('id', $item['id'])
                                ->where('product_id', $product->id)
                                ->update([
                                    'sort_order' => $index,
                                    'color' => $color
                                ]);
                        }
                        // Nếu là ảnh mới: Upload file và tạo bản ghi mới
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

            // Bước D: Đồng bộ lại ảnh đại diện (Lấy ảnh có sort_order thấp nhất làm Avatar)
            $firstImage = $product->images()->orderBy('sort_order', 'asc')->first();
            if ($firstImage) {
                $product->update(['image' => $firstImage->path]);
            } else {
                $product->update(['image' => null]);
            }

            return $product;
        });
    }

    /**
     * 4. Xóa sản phẩm và tất cả file ảnh liên quan
     */
    public function delete(Product $product): bool
    {
        // Xóa ảnh đại diện
        if ($product->image)
            Storage::disk('public')->delete($product->image);

        // Xóa tất cả ảnh trong bộ sưu tập
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        return $product->delete();
    }

    /**
     * 4b. Xóa nhiều sản phẩm cùng lúc
     */
    public function bulkDelete(array $ids): int
    {
        $products = Product::whereIn('id', $ids)->get();
        $count = 0;
        foreach ($products as $product) {
            if ($this->delete($product)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * 5. Sắp xếp lại thứ tự ảnh thủ công
     */
    public function reorderImages(Product $product, array $imageIds)
    {
        return DB::transaction(function () use ($product, $imageIds) {
            foreach ($imageIds as $index => $id) {
                $product->images()->where('id', $id)->update(['sort_order' => $index]);
            }
            // Cập nhật lại avatar sau khi đổi thứ tự
            $firstImage = $product->images()->orderBy('sort_order', 'asc')->first();
            if ($firstImage)
                $product->update(['image' => $firstImage->path]);

            return true;
        });
    }

    /**
     * 6. Đếm số lượng sản phẩm sắp hết hàng (Sắp hết/Cần nhập hàng)
     */
    public function countLowStock(): int
    {
        return Product::whereColumn('quantity', '<=', 'min_stock')->count();
    }

    /**
     * 7. Lấy danh sách ID của tất cả sản phẩm
     */
    public function getAllIds(array $filters): array
    {
        return Product::pluck('id')->toArray();
    }

    // =========================================================================
    // SECTION 2: FRONTEND / PUBLIC METHODS (Dành cho khách hàng)
    // =========================================================================

    /**
     * 8. Lấy sản phẩm bán chạy nhất gợi ý (Trang chủ/Tìm kiếm)
     */
    public function getBestSellingSuggestions(int $limit = 4)
    {
        // Kiểm tra cột sold_count (đã bán) có tồn tại không để ưu tiên sắp xếp
        $sortColumn = \Illuminate\Support\Facades\Schema::hasColumn('products', 'sold_count') ? 'sold_count' : 'created_at';

        return Product::query()
            ->with(['category', 'product_images'])
            ->where('status', 'active') // Chỉ lấy sản phẩm đang kinh doanh
            ->orderBy($sortColumn, 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * 9. Tìm chi tiết sản phẩm qua đường dẫn (Slug)
     */
    public function findActiveBySlug(string $slug)
    {
        return Product::where('slug', $slug)
            ->where('status', 'active')
            ->with([
                'category',
                'brand',
                'images' => function ($q) {
                    $q->orderBy('sort_order', 'asc');
                }
            ])
            ->firstOrFail();
    }

    /**
     * 10. Lấy danh sách sản phẩm theo danh mục (bao gồm cả danh mục con)
     */
    public function getProductsByCategory(string $categorySlug, int $perPage = 12, array $filters = [])
    {
        // Lấy thông tin danh mục hiện tại
        $category = Category::where('slug', $categorySlug)->firstOrFail();

        // Lấy danh sách ID của chính nó và toàn bộ các danh mục con (để hiển thị tất cả sản phẩm thuộc cây danh mục này)
        $categoryIds = $category->children()->pluck('id')->push($category->id);

        $query = Product::whereIn('category_id', $categoryIds)
            ->where('status', 'active');

        // Lọc theo khoảng giá
        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }
        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        // Xử lý sắp xếp (Giá tăng/giảm, Tên, Mới nhất)
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
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        return [
            'category' => $category,
            'products' => $query->paginate($perPage)->withQueryString()
        ];
    }

    // =========================================================================
    // SECTION 3: PRIVATE HELPERS (Các hàm bổ trợ nội bộ)
    // =========================================================================

    /**
     * Hàm chuẩn bị dữ liệu: Tạo Slug, SKU tự động và gộp các trường đặc thù vào Metadata (JSON)
     */
    private function prepareData(array $data, ?Product $product = null): array
    {
        // Tự động tạo Slug nếu để trống
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);
        }
        // Tự động tạo SKU nếu để trống (chỉ khi tạo mới)
        if (empty($data['sku']) && !$product) {
            $data['sku'] = 'SKU-' . strtoupper(Str::random(8));
        }

        // Xử lý tạo nhanh thương hiệu nếu brand_id dạng 'NEW:[Tên thương hiệu]'
        if (isset($data['brand_id']) && str_starts_with($data['brand_id'], 'NEW:')) {
            $brandName = str_replace('NEW:', '', $data['brand_id']);
            $newBrand = Brand::firstOrCreate(
                ['name' => $brandName],
                ['slug' => Str::slug($brandName), 'is_active' => true]
            );

            // Xử lý upload logo cho thương hiệu mới nếu có
            if (request()->hasFile('new_brand_logo')) {
                $path = request()->file('new_brand_logo')->store('brands', 'public');
                $newBrand->update(['logo' => $path]);
            }

            $data['brand_id'] = $newBrand->id;
        }

        // Xử lý thông số kỹ thuật: Lưu vào cột metadata dưới dạng JSON
        $metadata = $product ? ($product->metadata ?? []) : [];
        if (isset($data['specifications'])) {
            $metadata['specs'] = is_string($data['specifications']) ? json_decode($data['specifications'], true) : $data['specifications'];
        }
        $data['metadata'] = $metadata;

        // Xử lý mảng màu sắc nếu có
        if (isset($data['colors'])) {
            $data['colors'] = is_string($data['colors']) ? json_decode($data['colors'], true) : $data['colors'];
        }

        // Loại bỏ các key không có trong cấu trúc bảng products trước khi trả về để dùng cho create/update
        return array_diff_key($data, array_flip(self::NON_DB_KEYS));
    }

    /**
     * Hàm xử lý Upload ảnh vào bảng Gallery
     */
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