<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    /**
     * Helper: Lấy các key không phải là cột trong bảng Products
     */
    protected const NON_DB_KEYS = ['gallery', 'category_ids', 'specs', 'specifications', 'stock_locations'];

    /**
     * Listing products with filters
     */
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

    /**
     * Lấy danh sách ID khớp bộ lọc (cho Bulk Actions)
     */
    public function getAllIds(array $filters): array
    {
        return Product::query()->pluck('id')->toArray();
    }

    /**
     * Đếm sản phẩm tồn kho thấp
     */
    public function countLowStock(): int
    {
        return Product::whereColumn('quantity', '<=', 'min_stock')->count();
    }

    /**
     * Create Product with Transaction & Image Upload
     * @param array $data Dữ liệu đã bao gồm JSON decode cho metadata
     * @param array $categoryIds ID của các danh mục M2M
     */
    public function create(array $data, array $categoryIds = []): Product
    {
        return DB::transaction(function () use ($data, $categoryIds) {
            // 1. Auto generate slug & SKU
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);
            }
            if (empty($data['sku'])) {
                $data['sku'] = 'SKU-' . strtoupper(Str::random(8));
            }

            // 2. Handle Main Image Upload
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $data['image'] = $data['image']->store('products', 'public');
            }

            // 3. Create Product (Lọc bỏ các key không phải cột DB)
            $dataToCreate = array_diff_key($data, array_flip(self::NON_DB_KEYS));

            // Gán giá trị mặc định cho boolean nếu không có (do form submit thiếu field khi unchecked)
            $dataToCreate['is_featured'] = $dataToCreate['is_featured'] ?? false;
            $dataToCreate['is_active'] = $dataToCreate['is_active'] ?? false;

            $product = Product::create($dataToCreate);

            // 4. SYNC CATEGORIES (M2M)
            if (!empty($categoryIds)) {
                $product->categories()->sync($categoryIds);
            }

            // 5. Handle Gallery Images
            if (!empty($data['gallery']) && is_array($data['gallery'])) {
                foreach ($data['gallery'] as $file) {
                    $path = $file->store('products/gallery', 'public');
                    $product->images()->create(['path' => $path]);
                }
            }

            return $product;
        });
    }

    /**
     * Update Product
     */
    public function update(Product $product, array $data, array $categoryIds = []): Product
    {
        return DB::transaction(function () use ($product, $data, $categoryIds) {
            // 1. Handle Main Image Update
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                if ($product->image) Storage::disk('public')->delete($product->image);
                $data['image'] = $data['image']->store('products', 'public');
            }

            // 2. SYNC CATEGORIES
            $product->categories()->sync($categoryIds);

            // 3. UPDATE PRODUCT (Lọc bỏ các key không phải cột DB)
            $dataToUpdate = array_diff_key($data, array_flip(self::NON_DB_KEYS));
            $product->update($dataToUpdate);

            // 4. Handle Gallery (Add new ones)
            if (!empty($data['gallery']) && is_array($data['gallery'])) {
                foreach ($data['gallery'] as $file) {
                    $path = $file->store('products/gallery', 'public');
                    $product->images()->create(['path' => $path]);
                }
            }

            return $product;
        });
    }

    /**
     * Sắp xếp lại ảnh và cập nhật ảnh đại diện (Thumbnail)
     */
    public function reorderImages(Product $product, array $imageIds)
    {
        return DB::transaction(function () use ($product, $imageIds) {
            foreach ($imageIds as $index => $id) {
                $product->images()->where('id', $id)->update(['sort_order' => $index]);
            }

            $firstImage = $product->images()->orderBy('sort_order', 'asc')->first();

            if ($firstImage) {
                $product->update(['image' => $firstImage->path]);
            }

            return true;
        });
    }

    /**
     * Delete Product
     */
    public function delete(Product $product): bool
    {
        if ($product->image) Storage::disk('public')->delete($product->image);
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }

        return $product->delete();
    }
}
