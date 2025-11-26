<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    /**
     * Danh sách đơn hàng của tôi
     */
    public function index()
    {
        $user = Auth::user();

        // Lấy đơn hàng của user hiện tại, sắp xếp mới nhất
        $orders = Order::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Chi tiết đơn hàng
     */
    public function show($id)
    {
        $order = Order::where('user_id', Auth::id())
            ->with(['items', 'payment', 'shipment'])
            ->findOrFail($id);

        return view('customer.orders.show', compact('order'));
    }

    /**
     * Hủy đơn hàng (Chỉ khi pending)
     */
    public function cancel($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Không thể hủy đơn hàng này vì đã được xử lý.');
        }

        // Update status
        $order->update(['status' => 'cancelled']);

        // Logic hoàn tiền hoặc trả kho (nếu cần thiết) có thể thêm vào Service
        // $this->orderService->cancelOrder($order);

        return back()->with('success', 'Đã hủy đơn hàng thành công.');
    }
}
