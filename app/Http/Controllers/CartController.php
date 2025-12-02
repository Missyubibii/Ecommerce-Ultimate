<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected $cartService;
    protected $couponService;

    public function __construct(CartService $cartService, CouponService $couponService)
    {
        $this->cartService = $cartService;
        $this->couponService = $couponService;
    }

    public function index(Request $request)
    {
        $cartData = $this->cartService->getCartTotals();

        $debug = [
            'module' => 'Cart',
            'action' => 'View',
            'item_count' => $cartData['count'],
            'total' => $cartData['subtotal']
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $cartData,
                'debug' => $debug
            ]);
        }

        return view('cart.index', [
            'cart' => $cartData,
            'server_debug' => $debug
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1'
        ]);

        try {
            // 1. Lấy thông tin User và Session
            $userId = auth::id();
            $sessionId = $request->session()->getId();

            // 2. Truyền đủ 4 tham số theo thứ tự định nghĩa trong Service
            $this->cartService->addToCart(
                $userId,                    // Tham số 1
                $sessionId,                 // Tham số 2
                $request->product_id,       // Tham số 3 (productId)
                $request->input('quantity', 1) // Tham số 4
            );

            // 3. Truyền userId và sessionId vào getCartTotals để lấy đúng giỏ hàng
            $cartData = $this->cartService->getCartTotals($userId, $sessionId);

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào giỏ hàng',
                'cart_count' => $cartData['count'],
                'debug' => ['module' => 'Cart', 'action' => 'Add']
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->cartService->updateQuantity($id, $request->quantity);

            $cartData = $this->cartService->getCartTotals();

            return response()->json([
                'success' => true,
                'data' => $cartData, // Trả full data để client re-render list
                'debug' => ['module' => 'Cart', 'action' => 'UpdateQty']
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function remove(Request $request, $id)
    {
        try {
            $this->cartService->removeItem($id);
            $cartData = $this->cartService->getCartTotals();

            return response()->json([
                'success' => true,
                'data' => $cartData,
                'debug' => ['module' => 'Cart', 'action' => 'Remove']
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string|max:20']);

        try {
            // 1. Lấy tổng tiền (chỉ của các sản phẩm hợp lệ)
            $cartData = $this->cartService->getCartTotals();

            // Nếu giỏ hàng rỗng hoặc không có sản phẩm hợp lệ
            if ($cartData['subtotal'] == 0) {
                throw new \Exception("Giỏ hàng chưa đủ điều kiện áp dụng mã.");
            }

            // 2. Gọi CouponService để áp dụng mã
            $couponDetails = $this->couponService->applyCoupon($request->code, $cartData['subtotal']);

            return response()->json([
                'success' => true,
                'data' => $couponDetails
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
