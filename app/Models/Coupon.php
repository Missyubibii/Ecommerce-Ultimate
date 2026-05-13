<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'usage_limit',
        'used_count',
        'expiry_date',
        'is_active',
        'applicable_products',
        'applicable_categories',
        'applicable_brands',
        'channel'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'expiry_date' => 'datetime',
        'is_active' => 'boolean',
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'applicable_brands' => 'array',
    ];

    /**
     * Check xem coupon có hợp lệ không
     */
    public function isValid($cartTotal = 0)
    {
        if (!$this->is_active) return false;

        // Check thời gian
        $now = now();
        if ($this->expiry_date && $now->gt($this->expiry_date)) return false;

        // Check giới hạn sử dụng
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) return false;

        // Check giá trị đơn hàng tối thiểu
        if ($cartTotal < $this->min_order_amount) return false;

        return true;
    }

    /**
     * Tính toán số tiền được giảm
     */
    public function calculateDiscount($cartTotal)
    {
        if ($this->discount_type === 'fixed') {
            // Giảm cố định, nhưng không vượt quá tổng tiền (tránh âm tiền)
            return min($this->discount_value, $cartTotal);
        } elseif ($this->discount_type === 'percent') {
            return ($cartTotal * $this->discount_value) / 100;
        }
        return 0;
    }

    /**
     * Quan hệ với Đơn hàng (thông qua mã coupon)
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'coupon_code', 'code');
    }
}
