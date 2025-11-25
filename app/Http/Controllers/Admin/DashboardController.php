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

    public function index(Request $request)
    {
        $stats = $this->dashboardService->getStats();
        $recentOrders = $this->dashboardService->getRecentOrders();
        $chartData = $this->dashboardService->getRevenueChartData();

        $debug = [
            'module' => 'AdminDashboard',
            'action' => 'View',
            'stats' => $stats
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'recent_orders' => $recentOrders,
                'chart' => $chartData,
                'debug' => $debug
            ]);
        }

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'chartData' => $chartData,
            'server_debug' => $debug
        ]);
    }
}
