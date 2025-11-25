<?php

namespace App\Services;

use App\Models\Coupon;
use Exception;

class CouponService
{
    /**
     * ADMIN: Listing
     */
    public function getCoupons($perPage = 10)
    {
        return Coupon::orderByDesc('created_at')->paginate($perPage);
    }

    /**
     * ADMIN: Create/Update
     */
    public function saveCoupon($data, $id = null)
    {
        $data['code'] = strtoupper($data['code']); // Luôn uppercase code

        if ($id) {
            $coupon = Coupon::findOrFail($id);
            $coupon->update($data);
            return $coupon;
        }
        return Coupon::create($data);
    }

    /**
     * CLIENT: Kiểm tra và lấy thông tin coupon để apply vào giỏ hàng
     */
    public function applyCoupon($code, $cartTotal)
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (!$coupon) {
            throw new Exception("Mã giảm giá không tồn tại.");
        }

        if (!$coupon->isValid($cartTotal)) {
            // Ném ra lý do cụ thể thì tốt hơn, nhưng đơn giản hóa:
            if ($cartTotal < $coupon->min_order_amount) {
                $min = $coupon->min_order_amount ?? 0;
                throw new Exception("Đơn hàng phải tối thiểu " . number_format((float)$min, 0, ',', '.') . "đ để sử dụng mã này.");
            }
            throw new Exception("Mã giảm giá đã hết hạn hoặc không hợp lệ.");
        }

        $discountAmount = $coupon->calculateDiscount($cartTotal);

        return [
            'code' => $coupon->code,
            'description' => $coupon->description,
            'discount_amount' => $discountAmount,
            'new_total' => $cartTotal - $discountAmount
        ];
    }

    /**
     * SYSTEM: Ghi nhận coupon đã được sử dụng (gọi khi Place Order thành công)
     */
    public function recordUsage($code)
    {
        if (!$code) return;
        $coupon = Coupon::where('code', $code)->first();
        if ($coupon) {
            $coupon->increment('used_count');
        }
    }
}
