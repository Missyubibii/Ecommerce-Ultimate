<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'min_order_amount',
        'usage_limit',
        'used_count',
        'starts_at',
        'ends_at',
        'is_active'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Check xem coupon có hợp lệ không
     */
    public function isValid($cartTotal = 0)
    {
        if (!$this->is_active) return false;

        // Check thời gian
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;

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
        if ($this->type === 'fixed') {
            // Giảm cố định, nhưng không vượt quá tổng tiền (tránh âm tiền)
            return min($this->value, $cartTotal);
        } elseif ($this->type === 'percent') {
            return ($cartTotal * $this->value) / 100;
        }
        return 0;
    }
}
