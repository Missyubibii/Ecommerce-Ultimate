<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Tạo một instance của CouponService.
     */
    protected $couponService;

    /**
     * Tạo một instance của CouponService.
     */
    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * Hiển thị danh sách các mã giảm giá.
     */
    public function index()
    {
        // Lấy danh sách các mã giảm giá
        $coupons = $this->couponService->getCoupons();

        // Hiển thị view
        return view('admin.coupons.index', ['coupons' => $coupons]);
    }

    /**
     * Hiển thị form tạo mã giảm giá.
     */
    public function create()
    {
        // Hiển thị view
        return view('admin.coupons.create');
    }

    /**
     * Lưu mã giảm giá vào database.
     */
    public function store(Request $request)
    {
        //validate
        $request->validate([
            'code' => 'required|unique:coupons,code|max:20',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        //lưu coupon vào database
        $this->couponService->saveCoupon($request->all());

        return redirect()->route('admin.coupons.index')->with('success', 'Tạo mã giảm giá thành công');
    }

    /**
     * Hiển thị form chỉnh sửa mã giảm giá.
     */
    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', ['coupon' => $coupon]);
    }

    /**
     * Cập nhật mã giảm giá trong database.
     */
    public function update(Request $request, Coupon $coupon)
    {
        //validate
        $request->validate([
            'code' => 'required|max:20|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        //cập nhật coupon
        $this->couponService->saveCoupon($request->all(), $coupon->id);

        return redirect()->route('admin.coupons.index')->with('success', 'Cập nhật thành công');
    }

    /**
     * Xóa mã giảm giá.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Đã xóa mã giảm giá');
    }
}
