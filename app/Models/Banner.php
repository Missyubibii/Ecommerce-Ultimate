<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Banner extends Model
{
    protected $fillable = [
        'title', 'image', 'url', 'position',
        'sort_order', 'is_active', 'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Accessor lấy link ảnh
    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return 'https://placehold.co/1200x400/e2e8f0/1e293b?text=No+Image';
        }
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }
        return asset('storage/' . $this->image);
    }
}
