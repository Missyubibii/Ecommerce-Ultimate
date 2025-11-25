<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'q', 'date_from', 'date_to']);
        $orders = $this->orderService->getAdminOrders($filters);

        $statuses = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled', 'refunded'];

        $debug = [
            'module' => 'AdminOrder',
            'action' => 'List',
            'count' => $orders->count(),
            'filters' => $filters
        ];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $orders, 'debug' => $debug]);
        }

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => $statuses,
            'filters' => $filters,
            'server_debug' => $debug
        ]);
    }

    public function show(Request $request, $id)
    {
        $order = $this->orderService->getOrderDetails($id);
        $statuses = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled', 'refunded'];

        $debug = ['module' => 'AdminOrder', 'action' => 'Detail', 'order_id' => $id];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $order, 'debug' => $debug]);
        }

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => $statuses,
            'server_debug' => $debug
        ]);
    }

    // Cập nhật trạng thái Order chung
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,completed,cancelled,refunded'
        ]);

        $this->orderService->updateOrderStatus($id, $request->status);

        $debug = ['module' => 'AdminOrder', 'action' => 'UpdateStatus', 'id' => $id, 'new_status' => $request->status];

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Cập nhật thành công', 'debug' => $debug]);
        }

        return redirect()->route('admin.orders.show', $id)
            ->with('success', 'Trạng thái đơn hàng đã được cập nhật.')
            ->with('server_debug', $debug);
    }

    public function updatePayment(Request $request, $id)
    {
        $request->validate(['status' => 'required']);

        $this->orderService->updatePaymentStatus($id, $request->status);

        return back()->with('success', 'Trạng thái thanh toán đã được cập nhật.');
    }

    public function updateShipment(Request $request, $id)
    {
        // $id ở đây là shipment_id, không phải order_id
        $this->orderService->updateShipmentInfo(
            $id,
            $request->input('carrier'),
            $request->input('tracking_number'),
            $request->input('status')
        );

        return back()->with('success', 'Thông tin vận chuyển đã được cập nhật.');
    }
}
