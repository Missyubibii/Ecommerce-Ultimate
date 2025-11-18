<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Lấy danh sách category dạng cây (chỉ lấy root, load children)
     */
    public function getTree()
    {
        return Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();
    }

    /**
     * Lấy danh sách phẳng (cho dropdown chọn parent)
     */
    public function getAllFlat(): Collection
    {
        return Category::orderBy('name')->get();
    }

    public function create(array $data): Category
    {
        // Tự động tạo slug nếu không có
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Prevent setting parent to itself
        if (isset($data['parent_id']) && $data['parent_id'] == $category->id) {
            unset($data['parent_id']);
        }

        $category->update($data);
        return $category;
    }

    public function delete(Category $category): bool
    {
        // Logic phụ: Nếu xóa cha, con sẽ null parent_id (do migration set nullOnDelete)
        return $category->delete();
    }
}
