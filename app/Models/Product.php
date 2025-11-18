<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'sku', 'name', 'slug', 'description',
        'short_description', 'description',
        'price', 'cost_price', 'quantity', 'weight', 'status',
        'image', 'metadata', 'unit', 'min_stock'
    ];

    protected $casts = [
        'metadata' => 'array',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    // Quan hệ với Category (Module B)
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Quan hệ với Gallery
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    // Quan hệ tồn kho
    // public function inventoryMovements()
    // {
    //     // Placeholder cho quan hệ history nhập xuất
    //     // return $this->hasMany(InventoryLog::class);
    // }

    /**
     * Accessor: Tự động xử lý đường dẫn ảnh
     * Gọi trong view bằng: $product->image_url
     */
    public function getImageUrlAttribute()
    {
        // 1. Nếu không có ảnh -> Trả về ảnh placeholder mặc định
        if (empty($this->image)) {
            return 'https://placehold.co/600x400?text=No+Image';
        }

        // 2. Nếu là link ngoài (CDN) -> Trả về nguyên gốc
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        // 3. Nếu là ảnh upload local -> Trả về đường dẫn storage
        return asset('storage/' . $this->image);
    }
}
