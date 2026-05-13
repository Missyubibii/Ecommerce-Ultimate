<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function index()
    {
        $coupons = $this->couponService->getCoupons();
        return view('admin.coupons.index', ['coupons' => $coupons]);
    }

    public function create()
    {
        $products = Product::select('id', 'name')->get();
        $categories = Category::select('id', 'name')->get();
        $brands = Brand::select('id', 'name')->get();

        return view('admin.coupons.create', compact('products', 'categories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code|max:20',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expiry_date' => 'nullable|date|after:now',
            'channel' => 'required|in:email,system,both',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        
        $this->couponService->saveCoupon($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Tạo mã giảm giá thành công');
    }

    public function show(Coupon $coupon)
    {
        $coupon->loadCount('orders'); // Giả sử muốn xem đã dùng bao nhiêu lần thực tế qua đơn hàng
        
        // Lấy tên các đối tượng áp dụng để hiển thị
        $selectedProducts = Product::whereIn('id', $coupon->applicable_products ?? [])->get();
        $selectedCategories = Category::whereIn('id', $coupon->applicable_categories ?? [])->get();
        $selectedBrands = Brand::whereIn('id', $coupon->applicable_brands ?? [])->get();

        return view('admin.coupons.show', compact('coupon', 'selectedProducts', 'selectedCategories', 'selectedBrands'));
    }

    public function edit(Coupon $coupon)
    {
        $products = Product::select('id', 'name')->get();
        $categories = Category::select('id', 'name')->get();
        $brands = Brand::select('id', 'name')->get();

        return view('admin.coupons.edit', compact('coupon', 'products', 'categories', 'brands'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|max:20|unique:coupons,code,' . $coupon->id,
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expiry_date' => 'nullable|date',
            'channel' => 'required|in:email,system,both',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $this->couponService->saveCoupon($data, $coupon->id);

        return redirect()->route('admin.coupons.index')->with('success', 'Cập nhật thành công');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Đã xóa mã giảm giá');
    }
}
