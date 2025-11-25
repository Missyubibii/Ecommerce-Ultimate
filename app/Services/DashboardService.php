<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Lấy các chỉ số thống kê cơ bản (KPIs)
     */
    public function getStats()
    {
        $today = Carbon::today();

        return [
            'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total_amount'),
            'revenue_today' => Order::where('status', '!=', 'cancelled')->whereDate('created_at', $today)->sum('total_amount'),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_customers' => User::role('customer')->count(), // Giả sử dùng Spatie Permission
            'low_stock_products' => Product::whereColumn('quantity', '<=', 'min_stock')->count(),
        ];
    }

    /**
     * Lấy danh sách đơn hàng mới nhất
     */
    public function getRecentOrders($limit = 5)
    {
        return Order::with('user')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy dữ liệu biểu đồ doanh thu 7 ngày gần nhất
     */
    public function getRevenueChartData()
    {
        $data = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill những ngày không có đơn hàng bằng 0
        $chartData = [];
        $period = Carbon::now()->subDays(6)->daysUntil(Carbon::now())->toArray();

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $record = $data->firstWhere('date', $dateStr);
            $chartData['labels'][] = $date->format('d/m');
            $chartData['values'][] = $record ? $record->total : 0;
        }

        return $chartData;
    }
}
