<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    /**
     * Listing products with filters
     */
    public function listing(array $filters, int $perPage = 10)
    {
        $query = Product::with('category');

        // Filter: Search Keyword
        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['q']}%")
                    ->orWhere('sku', 'like', "%{$filters['q']}%");
            });
        }

        // Filter: Category
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Filter: Status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Sort
        $sort = $filters['sort'] ?? 'created_at';
        $direction = $filters['direction'] ?? 'desc';
        // Bảo vệ sort column
        if (in_array($sort, ['price', 'name', 'created_at', 'quantity'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
    }

    /**
     * Create Product with Transaction & Image Upload
     */
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // 1. Auto generate slug & SKU if missing
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

            // 3. Create Product
            $product = Product::create($data);

            // 4. Handle Gallery Images (Optional)
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
    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            // Handle Main Image Update
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                // Delete old image
                if ($product->image) Storage::disk('public')->delete($product->image);
                $data['image'] = $data['image']->store('products', 'public');
            }

            $product->update($data);

            // Handle Gallery (Add new ones)
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
     * Delete Product
     */
    public function delete(Product $product): bool
    {
        // Xóa ảnh vật lý nếu cần thiết (hoặc dùng Observer)
        if ($product->image) Storage::disk('public')->delete($product->image);

        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }

        return $product->delete();
    }

    public function countLowStock()
    {
        // Đếm các SP có quantity <= min_stock
        return Product::whereColumn('quantity', '<=', 'min_stock')->count();
    }

    public function getAllIds(array $filters)
    {
        // Lấy ID phục vụ cho chức năng "Chọn tất cả"
        // (Có thể tái sử dụng logic filter nếu cần chính xác tuyệt đối)
        return Product::pluck('id')->toArray();
    }

    /**
     * Sắp xếp lại ảnh và cập nhật ảnh đại diện (Thumbnail)
     */
    public function reorderImages(Product $product, array $imageIds)
    {
        return DB::transaction(function () use ($product, $imageIds) {
            // 1. Cập nhật sort_order cho từng ảnh
            foreach ($imageIds as $index => $id) {
                $product->images()->where('id', $id)->update(['sort_order' => $index]);
            }

            // 2. Tự động lấy ảnh đầu tiên làm ảnh đại diện
            // Logic: Lấy ảnh có sort_order = 0 (đứng đầu)
            $firstImage = $product->images()->orderBy('sort_order', 'asc')->first();

            if ($firstImage) {
                // Cập nhật ảnh đại diện của Product
                $product->update(['image' => $firstImage->path]);
            }

            return true;
        });
    }
}
