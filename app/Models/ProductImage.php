<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'path',
        'alt',
        'sort_order',
        'color'
    ];

    protected $appends = ['image_url'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlAttribute(): string
    {
        if (empty($this->path)) {
            return 'https://placehold.co/400x400?text=No+Image';
        }
        if (Str::startsWith($this->path, ['http://', 'https://'])) {
            return $this->path;
        }
        return asset('storage/' . $this->path);
    }
}
