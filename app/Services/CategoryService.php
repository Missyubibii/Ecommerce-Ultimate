<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * 1. Lấy danh sách danh mục theo cấu trúc cây (Parent - Children)
     * Mục đích: Hiển thị phân cấp trên menu hoặc danh sách quản lý
     */
    public function getTree()
    {
        // Lấy danh mục gốc (parent_id = null) và nạp kèm số lượng sản phẩm và danh mục con
        return Category::whereNull('parent_id')
            ->withCount('products')
            ->with([
                'children' => function ($query) {
                    $query->withCount('products');
                }
            ])
            ->orderBy('name')
            ->get();
    }

    /**
     * 2. Lấy danh sách phẳng tất cả danh mục
     * Mục đích: Dùng cho thẻ <select> (dropdown) khi chọn danh mục cha
     */
    public function getAllFlat(): Collection
    {
        return Category::orderBy('name')->get();
    }

    /**
     * 3. Tạo mới danh mục sản phẩm
     */
    public function create(array $data): Category
    {
        // Tự động tạo Slug từ tên danh mục nếu người dùng không nhập thủ công
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return Category::create($data);
    }

    /**
     * 4. Cập nhật thông tin danh mục
     */
    public function update(Category $category, array $data): Category
    {
        // Đồng bộ lại Slug nếu tên thay đổi hoặc slug bị xóa trống
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // LOGIC BẢO VỆ: Ngăn chặn việc gán danh mục cha chính là bản thân nó (tránh vòng lặp vô tận)
        if (isset($data['parent_id']) && $data['parent_id'] == $category->id) {
            unset($data['parent_id']);
        }

        $category->update($data);
        return $category;
    }

    /**
     * 5. Xóa danh mục
     */
    public function delete(Category $category): bool
    {
        /**
         * Lưu ý về logic phụ:
         * Dựa trên cấu trúc Migration, khi xóa danh mục cha, các danh mục con sẽ 
         * tự động được cập nhật parent_id = null (set null on delete).
         */
        return $category->delete();
    }
}