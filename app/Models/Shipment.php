<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Shipment extends Model
{
    use LogsActivity;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'carrier', 'tracking_number', 'shipped_at', 'delivered_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Vận chuyển đã được khởi tạo',
                'updated' => 'Thông tin vận chuyển đã được cập nhật',
                'deleted' => 'Vận chuyển đã bị xóa',
                default => "Vận chuyển {$eventName}"
            });
    }
    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
