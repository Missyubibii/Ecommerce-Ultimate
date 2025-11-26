<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $appends = ['image_url'];

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'cost_price',
        'quantity',
        'weight',
        'status',
        'image',
        'metadata',
        'unit',
        'min_stock',
        'is_active',
        'is_featured',
        'special_offer',
        'online_only'
    ];

    protected $casts = [
        'metadata' => 'array',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'special_offer' => 'boolean',
        'online_only' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return 'https://placehold.co/600x400?text=No+Image';
        }

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }
}
