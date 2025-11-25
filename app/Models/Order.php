<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'total_amount',
        'shipping_amount',
        'payment_method',
        'shipping_address',
        'billing_address',
        'metadata'
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'metadata' => 'array',
        'total_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ bổ sung cho Module G (Payments) - Nếu bạn đã tạo bảng payments
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Quan hệ bổ sung cho Module H (Shipments) - Nếu bạn đã tạo bảng shipments
    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }
}
