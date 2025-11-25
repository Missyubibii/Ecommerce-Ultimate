<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id', 'description'];

    // Quan hệ đệ quy: Lấy danh mục cha
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Quan hệ đệ quy: Lấy các danh mục con
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    // Quan hệ với sản phẩm
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
