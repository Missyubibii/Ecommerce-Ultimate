<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Brand extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Thương hiệu đã được tạo mới',
                'updated' => 'Thương hiệu đã được cập nhật',
                'deleted' => 'Thương hiệu đã bị xóa',
                default => "Thương hiệu {$eventName}"
            });
    }
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'is_active',
        'display_locations',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_locations' => 'array',
        'sort_order' => 'integer',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getLogoUrlAttribute()
    {
        if (empty($this->logo)) {
            return 'https://placehold.co/200x200?text=' . urlencode($this->name);
        }

        if (Str::startsWith($this->logo, ['http://', 'https://'])) {
            return $this->logo;
        }

        return asset('storage/' . $this->logo);
    }
}
