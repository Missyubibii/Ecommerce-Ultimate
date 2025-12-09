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
        $userId = Auth::id();
        $sessionId = $request->session()->getId();
        $cartData = $this->cartService->getCart($userId, $sessionId);

        $debug = [
            'module' => 'Cart',
            'action' => 'View',
            'item_count' => $cartData['count'] ?? 0,
            'total' => $cartData['subtotal'] ?? 0
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
            $userId = auth::id();
            $sessionId = $request->session()->getId();

            // Thêm vào giỏ
            $this->cartService->addToCart(
                $userId,
                $sessionId,
                $request->product_id,
                $request->input('quantity', 1)
            );

            // Lấy lại toàn bộ giỏ hàng để cập nhật UI
            $cartData = $this->cartService->getCart($userId, $sessionId);

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào giỏ hàng',
                'data' => $cartData,
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
            $cartData = $this->cartService->updateQty($id, $request->quantity);

            return response()->json([
                'success' => true,
                'data' => $cartData,
                'debug' => ['module' => 'Cart', 'action' => 'UpdateQty']
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function remove(Request $request, $id)
    {
        try {
            $cartData = $this->cartService->removeItem($id);

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
            $userId = Auth::id();
            $sessionId = $request->session()->getId();
            $cartData = $this->cartService->getCart($userId, $sessionId);

            if (($cartData['subtotal'] ?? 0) == 0) {
                throw new \Exception("Giỏ hàng chưa đủ điều kiện áp dụng mã.");
            }

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
