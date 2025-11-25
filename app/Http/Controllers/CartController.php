<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
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
            $this->cartService->addToCart(
                $request->product_id,
                $request->input('quantity', 1)
            );

            // Trả về dữ liệu giỏ hàng mới nhất để UI update
            $cartData = $this->cartService->getCartTotals();

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào giỏ hàng',
                'cart_count' => $cartData['count'], // Để update icon trên header
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
}
