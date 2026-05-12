<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\ChatSession;
use App\Models\SearchLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class DashboardService
{
    protected $timezone = 'Asia/Ho_Chi_Minh';

    /**
     * Lấy các chỉ số doanh thu (Sales Overview)
     */
    public function getSalesStats($startDate = null, $endDate = null)
    {
        $cacheKey = 'dashboard_sales_stats_' . ($startDate ? $startDate->format('Ymd') : 'all') . '_' . ($endDate ? $endDate->format('Ymd') : 'all');
        return Cache::remember($cacheKey, 600, function () use ($startDate, $endDate) {
            $now = Carbon::now($this->timezone);

            // Logic doanh thu: Đơn có payment 'paid' HOẶC status 'completed'/'processing'
            $baseQuery = Order::where(function ($query) {
                $query->whereHas('payment', function ($q) {
                    $q->whereNotNull('paid_at');
                })->orWhereIn('status', ['completed', 'processing']);
            });

            if ($startDate && $endDate) {
                $diffInDays = $startDate->diffInDays($endDate) + 1;
                $prevStartDate = $startDate->copy()->subDays($diffInDays);
                $prevEndDate = $endDate->copy()->subDays($diffInDays);

                $revenueToday = (float) (clone $baseQuery)->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])->sum('total_amount');
                $revenueYesterday = (float) (clone $baseQuery)->whereBetween('created_at', [$prevStartDate->copy()->startOfDay(), $prevEndDate->copy()->endOfDay()])->sum('total_amount');

                $monthStart = $endDate->copy()->startOfMonth();
                $monthEnd = $endDate->copy()->endOfMonth();
                $lastMonthStart = $monthStart->copy()->subMonth()->startOfMonth();
                $lastMonthEnd = $monthStart->copy()->subMonth()->endOfMonth();

                $revenueMonth = (float) (clone $baseQuery)->whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');
                $revenueLastMonth = (float) (clone $baseQuery)->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->sum('total_amount');

                $totalRevenue = (float) $baseQuery->sum('total_amount');
                $completedOrdersCount = (clone $baseQuery)->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])->count();
                $aovRevenue = $revenueToday; 
            } else {
                $today = $now->copy()->startOfDay();
                $yesterday = $now->copy()->subDay()->startOfDay();
                $thisMonth = $now->copy()->startOfMonth();
                $lastMonth = $now->copy()->subMonth()->startOfMonth();

                $totalRevenue = (float) $baseQuery->sum('total_amount');
                $revenueToday = (float) (clone $baseQuery)->whereDate('created_at', $today)->sum('total_amount');
                $revenueYesterday = (float) (clone $baseQuery)->whereDate('created_at', $yesterday)->sum('total_amount');
                $revenueMonth = (float) (clone $baseQuery)->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->sum('total_amount');
                $revenueLastMonth = (float) (clone $baseQuery)->whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->sum('total_amount');
                
                $completedOrdersCount = (clone $baseQuery)->count();
                $aovRevenue = $totalRevenue;
            }

            // Tính % thay đổi so với hôm qua/kỳ trước
            $todayChange = $revenueYesterday > 0 ? (($revenueToday - $revenueYesterday) / $revenueYesterday) * 100 : ($revenueToday > 0 ? 100 : 0);
            // Tính % thay đổi so với tháng trước
            $monthChange = $revenueLastMonth > 0 ? (($revenueMonth - $revenueLastMonth) / $revenueLastMonth) * 100 : ($revenueMonth > 0 ? 100 : 0);

            // AOV (Average Order Value)
            $aov = $completedOrdersCount > 0 ? $aovRevenue / $completedOrdersCount : 0;

            return [
                'total_revenue' => $totalRevenue,
                'revenue_today' => $revenueToday,
                'revenue_today_change' => $todayChange,
                'revenue_month' => $revenueMonth,
                'revenue_month_change' => $monthChange,
                'aov' => $aov,
            ];
        });
    }


    /**
     * Lấy thống kê đơn hàng theo trạng thái
     */
    public function getOrderStats($startDate = null, $endDate = null)
    {
        $cacheKey = 'dashboard_order_stats_' . ($startDate ? $startDate->format('Ymd') : 'all') . '_' . ($endDate ? $endDate->format('Ymd') : 'all');
        return Cache::remember($cacheKey, 600, function () use ($startDate, $endDate) {
            $query = Order::query();
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);
            }
            return [
                'pending' => (clone $query)->where('status', 'pending')->count(), // Chờ xác nhận
                'processing' => (clone $query)->where('status', 'processing')->count(), // Đang xử lý
                'completed' => (clone $query)->where('status', 'completed')->count(),
                'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            ];
        });
    }


    /**
     * Lấy dữ liệu biểu đồ doanh thu 30 ngày gần nhất
     */
    public function getRevenueChartData($days = 30)
    {
        return Cache::remember("dashboard_revenue_chart_{$days}", 600, function () use ($days) {
            $now = Carbon::now($this->timezone);
            $startDate = $now->copy()->subDays($days - 1)->startOfDay();

            $data = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
                ->where(function ($query) {
                    $query->whereHas('payment', function ($q) {
                        $q->whereNotNull('paid_at');
                    })->orWhereIn('status', ['completed', 'processing']);
                })
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $chartData = ['labels' => [], 'values' => []];
            for ($i = 0; $i < $days; $i++) {
                $currentDate = $startDate->copy()->addDays($i);
                $dateStr = $currentDate->format('Y-m-d');
                $record = $data->firstWhere('date', $dateStr);
                $chartData['labels'][] = $currentDate->format('d/m');
                $chartData['values'][] = $record ? (float) $record->total : 0;
            }

            return $chartData;
        });
    }

    /**
     * Lấy sản phẩm bán chạy nhất
     */
    public function getTopProducts($limit = 5)
    {
        return Cache::remember("dashboard_top_products_{$limit}", 600, function () use ($limit) {
            return DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->select('products.name', 'categories.name as category_name', DB::raw('SUM(order_items.quantity) as sold_count'))
                ->groupBy('products.id', 'products.name', 'categories.name')
                ->orderByDesc('sold_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Lấy danh sách sản phẩm tồn kho thấp (quantity <= 5)
     */
    public function getLowStockProducts($limit = 10)
    {
        return Product::where('quantity', '<=', 5)
            ->orderBy('quantity', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy từ khóa tìm kiếm hàng đầu trong 7 ngày
     */
    public function getTopSearchKeywords($days = 7, $limit = 5)
    {
        return Cache::remember("dashboard_top_search_{$days}_{$limit}", 600, function () use ($days, $limit) {
            return SearchLog::where('created_at', '>=', Carbon::now($this->timezone)->subDays($days))
                ->select('keyword', DB::raw('count(*) as count'))
                ->groupBy('keyword')
                ->orderByDesc('count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Thống kê khách hàng
     */
    public function getCustomerStats()
    {
        return Cache::remember('dashboard_customer_stats', 600, function () {
            $now = Carbon::now($this->timezone);
            return [
                'total' => User::role('customer')->count(),
                'new_this_month' => User::role('customer')
                    ->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year)
                    ->count(),
            ];
        });
    }

    /**
     * Thống kê Chat Sessions (Phải có ít nhất 1 tin nhắn)
     */
    public function getChatStats($startDate = null, $endDate = null)
    {
        $query = ChatSession::has('messages');
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);
        }
        return [
            'open_unassigned' => $query->count(),
        ];
    }


    /**
     * Hoạt động Admin gần đây
     */
    public function getRecentActivities($limit = 5)
    {
        return Activity::with('causer')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Đơn hàng gần đây
     */
    public function getRecentOrders($limit = 10)
    {
        return Order::with(['user', 'payment'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Xóa cache Dashboard (gọi khi có event lớn)
     */
    public function clearCache()
    {
        Cache::flush(); // Dùng flush để xóa tất cả cache vì có nhiều key động theo ngày
    }

}

