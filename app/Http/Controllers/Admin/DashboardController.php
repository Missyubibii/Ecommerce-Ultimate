<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Hiển thị dashboard tổng quan
     */
    public function index(Request $request)
    {
        $searchDays = $request->get('search_days', 7);

        // Date Filter Logic
        $filterType = $request->get('filter_type', 'default');
        $startDate = null;
        $endDate = null;

        $now = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');

        if ($filterType === '1_day') {
            $startDate = $now->copy();
            $endDate = $now->copy();
        } elseif ($filterType === '7_days') {
            $startDate = $now->copy()->subDays(6);
            $endDate = $now->copy();
        } elseif ($filterType === 'month') {
            $startDate = $now->copy()->startOfMonth();
            $endDate = $now->copy();
        } elseif ($filterType === 'custom' && $request->has('start_date') && $request->has('end_date')) {
            $startDate = \Carbon\Carbon::parse($request->get('start_date'), 'Asia/Ho_Chi_Minh');
            $endDate = \Carbon\Carbon::parse($request->get('end_date'), 'Asia/Ho_Chi_Minh');
        }

        // Nếu là AJAX request cho search keywords (chỉ cập nhật search)
        if ($request->ajax() && $request->has('search_days') && !$request->has('realtime')) {
            return response()->json([
                'topKeywords' => $this->dashboardService->getTopSearchKeywords($searchDays, 20),
                'searchDays' => $searchDays
            ]);
        }

        // Nếu là yêu cầu realtime, xóa cache trước khi lấy dữ liệu
        if ($request->has('realtime') || $request->has('filter_type')) {
            $this->dashboardService->clearCache();
        }

        // Thu thập dữ liệu từ service
        $data = [
            'salesStats'     => $this->dashboardService->getSalesStats($startDate, $endDate),
            'orderStats'     => $this->dashboardService->getOrderStats($startDate, $endDate),
            'revenueChart'   => $this->dashboardService->getRevenueChartData(30),
            'topProducts'    => $this->dashboardService->getTopProducts(15),
            'recentOrders'   => $this->dashboardService->getRecentOrders(20),
            'lowStock'       => $this->dashboardService->getLowStockProducts(15),
            'topKeywords'    => $this->dashboardService->getTopSearchKeywords($searchDays, 20),
            'searchDays'     => $searchDays,
            'customerStats'  => $this->dashboardService->getCustomerStats(), // keeping original
            'chatStats'      => $this->dashboardService->getChatStats($startDate, $endDate),
            'activities'     => $this->dashboardService->getRecentActivities(20),
            'last_updated'   => now()->format('H:i:s'),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'html' => [
                    'recent_orders' => view('components.dashboard.recent-orders-table', ['recentOrders' => $data['recentOrders']])->render(),
                    'low_stock' => view('components.dashboard.low-stock-table', ['lowStock' => $data['lowStock']])->render(),
                    'activity_logs' => view('components.dashboard.activity-logs', ['activities' => $data['activities']])->render(),
                ]
            ]);
        }

        return view('admin.dashboard', $data);
    }

    /**
     * Thủ công xóa cache dashboard
     */
    public function refresh(Request $request)
    {
        $this->dashboardService->clearCache();
        
        // Log hành động
        activity('admin')
            ->causedBy(auth()->user())
            ->log('Admin đã làm mới dữ liệu Dashboard (Clear Cache)');

        return back()->with('success', 'Dữ liệu Dashboard đã được làm mới!');
    }
}
