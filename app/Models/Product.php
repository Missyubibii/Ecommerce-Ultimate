<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use App\Models\Brand;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use LogsActivity;

    protected $appends = ['image_url'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'sku', 'price', 'quantity', 'category_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Sản phẩm đã được tạo mới',
                'updated' => 'Sản phẩm đã được cập nhật thông tin',
                'deleted' => 'Sản phẩm đã bị xóa',
                default => "Sản phẩm {$eventName}"
            });
    }

    protected $fillable = [
        'category_id',
        'brand_id',
        'sku',
        'model_code',
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'cost_price',
        'market_price',
        'warranty',
        'production_year',
        'origin',
        'condition',
        'special_offer',
        'quantity',
        'weight',
        'status',
        'image',
        'metadata',
        'colors',
        'unit',
        'min_stock',
        'is_featured',
    ];

    protected $casts = [
        'metadata' => 'array',
        'colors' => 'array',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'market_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'special_offer' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function product_images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order', 'asc');
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
