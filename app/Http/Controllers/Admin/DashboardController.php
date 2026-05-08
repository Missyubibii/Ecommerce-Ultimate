<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //Hiển thị dashboard
    protected $dashboardService;

    //tạo instance của DashboardService
    public function __construct(DashboardService $dashboardService)
    {
        //gán instance
        $this->dashboardService = $dashboardService;
    }

    //Hiển thị dashboard
    public function index(Request $request)
    {
        //lấy thống kê
        $stats = $this->dashboardService->getStats();
        $recentOrders = $this->dashboardService->getRecentOrders();
        $chartData = $this->dashboardService->getRevenueChartData();

        //debug
        $debug = [
            'module' => 'AdminDashboard',
            'action' => 'View',
            'stats' => $stats
        ];

        //hiển thị json
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'recent_orders' => $recentOrders,
                'chart' => $chartData,
                'debug' => $debug
            ]);
        }

        //hiển thị view
        return view('admin.dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'chartData' => $chartData,
            'server_debug' => $debug
        ]);
    }
}
