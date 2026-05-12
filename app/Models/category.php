<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Category extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'parent_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Danh mục đã được tạo mới',
                'updated' => 'Danh mục đã được cập nhật',
                'deleted' => 'Danh mục đã bị xóa',
                default => "Danh mục {$eventName}"
            });
    }
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
