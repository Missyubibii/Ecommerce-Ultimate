<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CheckoutController extends Controller
{
    protected $cartService;
    protected $orderService;

    public function __construct(CartService $cartService, OrderService $orderService)
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $cartData = $this->cartService->getCartTotals();

        if ($cartData['count'] == 0) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống');
        }

        $user = Auth::user();
        $addresses = $user ? $user->addresses : [];

        $debug = [
            'module' => 'Checkout',
            'action' => 'Init',
            'user_id' => $user ? $user->id : 'Guest',
            'cart_total' => $cartData['subtotal']
        ];

        return view('checkout.index', [
            'cart' => $cartData,
            'addresses' => $addresses,
            'user' => $user,
            'server_debug' => $debug
        ]);
    }

    /**
     * Xử lý đặt hàng (POST /checkout/place-order)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. Validate Dynamic
        $rules = [
            'payment_method' => 'required|in:cod,banking',
            'address_id' => 'nullable|exists:addresses,id',
        ];

        // Nếu là Guest hoặc User chọn "Địa chỉ mới", bắt buộc nhập thông tin
        if (!$user || !$request->input('address_id')) {
            $rules['new_address.full_name'] = 'required|string|max:255';
            $rules['new_address.phone'] = 'required|string|max:20';
            $rules['new_address.address_line1'] = 'required|string|max:255';
            $rules['new_address.city'] = 'required|string|max:100';
        }

        // Nếu là Guest, bắt buộc phải có Email để gửi đơn hàng
        if (!$user) {
            $rules['email'] = 'required|email|max:255';
        }

        $request->validate($rules);

        try {
            // 2. Merge Guest Email vào payload nếu có
            $payload = $request->all();
            if (!$user && $request->has('email')) {
                // Lưu email guest vào metadata hoặc shipping address để liên hệ
                $payload['guest_email'] = $request->email;
            }

            // 3. Gọi Service xử lý
            $order = $this->orderService->placeOrder($user, $payload);

            $debug = [
                'module' => 'Order',
                'action' => 'PlaceOrder',
                'status' => 'Success',
                'order_id' => $order->id,
                'amount' => $order->total_amount
            ];

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đặt hàng thành công!',
                    'redirect_url' => route('checkout.thankyou', $order->id),
                    'debug' => $debug
                ]);
            }

            return redirect()->route('checkout.thankyou', $order->id)
                ->with('server_debug', $debug);
        } catch (\Exception $e) {
            $debug = ['module' => 'Order', 'action' => 'PlaceOrder', 'status' => 'Failed', 'error' => $e->getMessage()];

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage(), 'debug' => $debug], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()])->withInput()->with('server_debug', $debug);
        }
    }

    /**
     * Trang Cảm ơn (GET /checkout/thankyou/{id})
     */
    public function thankyou(Request $request, $id)
    {
        $order = \App\Models\Order::with('items')->findOrFail($id);

        // Security check đơn giản: Nếu là user login, phải đúng chủ sở hữu
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403);
        }

        $debug = ['module' => 'Order', 'action' => 'ViewThankYou', 'order_number' => $order->order_number];

        return view('checkout.thankyou', [
            'order' => $order,
            'server_debug' => $debug
        ]);
    }
}
