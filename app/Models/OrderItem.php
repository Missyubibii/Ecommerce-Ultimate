<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_snapshot',
        'unit_price',
        'quantity',
        'subtotal'
    ];

    protected $casts = [
        'product_snapshot' => 'array',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Helper để lấy tên sản phẩm từ snapshot kể cả khi product gốc bị xóa
    public function getProductNameAttribute()
    {
        return $this->product_snapshot['name'] ?? 'Sản phẩm không tồn tại';
    }
}
