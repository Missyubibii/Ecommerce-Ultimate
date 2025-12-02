<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Models\CartItem;
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
        $carts = $this->cartService->getAdminCartsListing();

        $debug = [
            'module' => 'AdminCart',
            'action' => 'List',
            'count' => $carts->count()
        ];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $carts, 'debug' => $debug]);
        }

        return view('admin.carts.index', [
            'carts' => $carts,
            'server_debug' => $debug
        ]);
    }

    public function show(Request $request)
    {
        // Lấy param từ query string (user_id hoặc session_id)
        $userId = $request->query('user_id');
        $sessionId = $request->query('session_id');

        $items = $this->cartService->getAdminCartDetails($userId, $sessionId);

        // Xác định chủ sở hữu để hiển thị tên
        $owner = 'Khách vãng lai (' . \Illuminate\Support\Str::limit($sessionId, 10) . ')';
        if ($items->first() && $items->first()->user) {
            $owner = $items->first()->user->name . ' (' . $items->first()->user->email . ')';
        }

        $debug = [
            'module' => 'AdminCart',
            'action' => 'Detail',
            'owner' => $owner,
            'items_count' => $items->count()
        ];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $items, 'debug' => $debug]);
        }

        return view('admin.carts.show', [
            'items' => $items,
            'owner' => $owner,
            'userId' => $userId,
            'sessionId' => $sessionId,
            'cartTotal' => $items->sum('total'),
            'server_debug' => $debug
        ]);
    }

    public function destroy(Request $request)
    {
        $userId = $request->input('user_id');
        $sessionId = $request->input('session_id');

        $this->cartService->clearCartByAdmin($userId, $sessionId);

        $debug = ['module' => 'AdminCart', 'action' => 'Clear', 'target' => $userId ?? $sessionId];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã xóa giỏ hàng', 'debug' => $debug]);
        }

        return redirect()->route('admin.carts.index')
            ->with('success', 'Đã xóa giỏ hàng thành công')
            ->with('server_debug', $debug);
    }
}
