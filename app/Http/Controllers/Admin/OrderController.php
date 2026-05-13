<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Tạo instance của OrderService
     * @var 
     */
    protected $orderService;

    /**
     * Gán instance của OrderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Hiển thị danh sách đơn hàng
     */
    public function index(Request $request)
    {
        //Lấy các filter từ request
        $filters = $request->only(['status', 'q', 'date_from', 'date_to', 'sort', 'direction']);
        $orders = $this->orderService->getAdminOrders($filters);

        //Lấy các trạng thái đơn hàng
        $statuses = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled', 'refunded'];

        //Debug
        $debug = [
            'module' => 'AdminOrder',
            'action' => 'List',
            'count' => $orders->count(),
            'filters' => $filters
        ];

        //Hiển thị json
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $orders, 'debug' => $debug]);
        }

        //Hiển thị view
        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => $statuses,
            'filters' => $filters,
            'server_debug' => $debug
        ]);
    }

    /**
     * Hiển thị thông tin chi tiết đơn hàng
     */
    public function show(Request $request, $id)
    {
        //Lấy thông tin đơn hàng
        $order = $this->orderService->getOrderDetails($id);

        //Lấy các trạng thái đơn hàng
        $statuses = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled', 'refunded'];

        //Debug
        $debug = ['module' => 'AdminOrder', 'action' => 'Detail', 'order_id' => $id];

        //Hiển thị json
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $order, 'debug' => $debug]);
        }

        // Lấy lịch sử hoạt động (Activity Logs)
        $activities = \Spatie\Activitylog\Models\Activity::where(function($q) use ($order) {
            $q->where(function($sq) use ($order) {
                $sq->where('subject_type', Order::class)->where('subject_id', $order->id);
            });
            if ($order->payment) {
                $q->orWhere(function($sq) use ($order) {
                    $sq->where('subject_type', \App\Models\Payment::class)->where('subject_id', $order->payment->id);
                });
            }
            if ($order->shipment) {
                $q->orWhere(function($sq) use ($order) {
                    $sq->where('subject_type', \App\Models\Shipment::class)->where('subject_id', $order->shipment->id);
                });
            }
        })
        ->with('causer')
        ->latest()
        ->get();

        //Hiển thị view
        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => $statuses,
            'activities' => $activities,
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
            ->with('success', 'Trạng thái đơn hàng đã được cập nhật. Đã gửi email thông báo cho khách hàng.')
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

    /**
     * Pipeline: Xác nhận đơn hàng
     */
    public function approve($id)
    {
        try {
            $this->orderService->approveOrder($id);
            return back()->with('success', 'Đơn hàng đã được xác nhận. Đã gửi email thông báo cho khách hàng.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Pipeline: Giao hàng
     */
    public function ship(Request $request, $id)
    {
        $request->validate([
            'carrier' => 'required|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
        ]);

        try {
            $this->orderService->shipOrder($id, $request->only(['carrier', 'tracking_number']));
            return back()->with('success', 'Đơn hàng đã chuyển sang trạng thái đang giao. Đã gửi email thông báo cho khách hàng.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Pipeline: Hoàn thành
     */
    public function complete($id)
    {
        try {
            $this->orderService->completeOrder($id);
            return back()->with('success', 'Đơn hàng đã hoàn thành thành công. Đã gửi email thông báo cho khách hàng.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Pipeline: Hủy đơn
     */
    public function cancel(Request $request, $id)
    {
        try {
            $this->orderService->cancelOrder($id, $request->input('reason'));
            return back()->with('success', 'Đơn hàng đã được hủy. Đã gửi email thông báo cho khách hàng.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
