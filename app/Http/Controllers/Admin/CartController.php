<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Tạo một instance của CartService.
     */
    protected $cartService;

    /**
     * Tạo một instance của CartService.
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Hiển thị danh sách các giỏ hàng.
     */
    public function index(Request $request)
    {
        // Lấy danh sách các giỏ hàng
        $carts = $this->cartService->getAdminCartsListing();

        // Debug
        $debug = [
            'module' => 'AdminCart',
            'action' => 'List',
            'count' => $carts->count()
        ];

        // Hiển thị danh sách các giỏ hàng
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $carts, 'debug' => $debug]);
        }

        // Hiển thị view
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

        // Lấy chi tiết giỏ hàng
        $items = $this->cartService->getAdminCartDetails($userId, $sessionId);

        // Xác định chủ sở hữu để hiển thị tên
        $owner = 'Khách vãng lai (' . \Illuminate\Support\Str::limit($sessionId, 10) . ')';
        if ($items->first() && $items->first()->user) {
            $owner = $items->first()->user->name . ' (' . $items->first()->user->email . ')';
        }

        // Debug
        $debug = [
            'module' => 'AdminCart',
            'action' => 'Detail',
            'owner' => $owner,
            'items_count' => $items->count()
        ];

        // Hiển thị chi tiết giỏ hàng
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $items, 'debug' => $debug]);
        }

        // Hiển thị view
        return view('admin.carts.show', [
            'items' => $items,
            'owner' => $owner,
            'userId' => $userId,
            'sessionId' => $sessionId,
            'cartTotal' => $items->sum('total'),
            'server_debug' => $debug
        ]);
    }

    /**
     * Xóa giỏ hàng.
     */
    public function destroy(Request $request)
    {
        // Lấy param từ query string (user_id hoặc session_id)
        $userId = $request->input('user_id');
        $sessionId = $request->input('session_id');

        // Xóa giỏ hàng
        $this->cartService->clearCartByAdmin($userId, $sessionId);

        // Debug
        $debug = ['module' => 'AdminCart', 'action' => 'Clear', 'target' => $userId ?? $sessionId];

        // Hiển thị json
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã xóa giỏ hàng', 'debug' => $debug]);
        }

        return redirect()->route('admin.carts.index')
            ->with('success', 'Đã xóa giỏ hàng thành công')
            ->with('server_debug', $debug);
    }
}
