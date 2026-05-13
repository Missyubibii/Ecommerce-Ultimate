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

    protected function getCacheKey($prefix, $startDate, $endDate)
    {
        $startStr = $startDate ? $startDate->format('Ymd') : 'all';
        $endStr = $endDate ? $endDate->format('Ymd') : 'all';
        return "{$prefix}_{$startStr}_{$endStr}";
    }

    /**
     * Lấy các chỉ số doanh thu (Sales Overview)
     */
    public function getSalesStats($startDate = null, $endDate = null)
    {
        $cacheKey = $this->getCacheKey('dashboard_sales_stats', $startDate, $endDate);
        return Cache::remember($cacheKey, 300, function () use ($startDate, $endDate) {
            $baseQuery = Order::whereHas('payment', function ($q) {
                $q->whereNotNull('paid_at');
            });

            $revenueQuery = clone $baseQuery;
            $completedOrdersQuery = clone $baseQuery;

            if ($startDate && $endDate) {
                $revenueQuery->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);
                $completedOrdersQuery->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);
            }

            $revenueToday = (float) $revenueQuery->sum('total_amount');
            $completedOrdersCount = $completedOrdersQuery->count();

            // Tính % thay đổi so với kỳ trước
            $todayChange = 'N/A';
            if ($startDate && $endDate) {
                $diffInDays = $startDate->diffInDays($endDate) + 1;
                $prevStartDate = $startDate->copy()->subDays($diffInDays)->startOfDay();
                $prevEndDate = $endDate->copy()->subDays($diffInDays)->endOfDay();

                $revenueYesterday = (float) (clone $baseQuery)
                    ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
                    ->sum('total_amount');

                if ($revenueYesterday > 0) {
                    $todayChange = round((($revenueToday - $revenueYesterday) / $revenueYesterday) * 100, 1);
                } elseif ($revenueYesterday == 0 && $revenueToday > 0) {
                    $todayChange = 100;
                }
            }

            // AOV (Average Order Value)
            $aov = $completedOrdersCount > 0 ? $revenueToday / $completedOrdersCount : 0;

            return [
                'revenue_today' => $revenueToday,
                'revenue_today_change' => $todayChange,
                'aov' => $aov,
                'completed_orders_count' => $completedOrdersCount,
            ];
        });
    }

    /**
     * Lấy thống kê đơn hàng theo trạng thái
     */
    public function getOrderStats($startDate = null, $endDate = null)
    {
        $cacheKey = $this->getCacheKey('dashboard_order_stats', $startDate, $endDate);
        return Cache::remember($cacheKey, 300, function () use ($startDate, $endDate) {
            $query = Order::query();
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);
            }
            return [
                'pending' => (clone $query)->where('status', 'pending')->count(),
                'processing' => (clone $query)->where('status', 'processing')->count(),
                'completed' => (clone $query)->where('status', 'completed')->count(),
                'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            ];
        });
    }

    /**
     * Lấy dữ liệu biểu đồ doanh thu theo khoảng thời gian
     */
    public function getRevenueChartData($startDate = null, $endDate = null)
    {
        $cacheKey = $this->getCacheKey('dashboard_revenue_chart', $startDate, $endDate);
        return Cache::remember($cacheKey, 300, function () use ($startDate, $endDate) {
            $now = Carbon::now($this->timezone);
            if (!$startDate || !$endDate) {
                $startDate = $now->copy()->subDays(29)->startOfDay();
                $endDate = $now->copy()->endOfDay();
            }

            $diffDays = $startDate->diffInDays($endDate) + 1;
            if ($diffDays > 90) { 
                $diffDays = 90;
                $startDate = $endDate->copy()->subDays(89)->startOfDay();
            }

            $data = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
                ->whereHas('payment', function ($q) {
                    $q->whereNotNull('paid_at');
                })
                ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $chartData = ['labels' => [], 'values' => []];
            for ($i = 0; $i < $diffDays; $i++) {
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
     * Lấy sản phẩm bán chạy nhất trong kỳ
     */
    public function getTopProducts($startDate = null, $endDate = null, $limit = 5)
    {
        $cacheKey = $this->getCacheKey("dashboard_top_products_{$limit}", $startDate, $endDate);
        return Cache::remember($cacheKey, 300, function () use ($startDate, $endDate, $limit) {
            $query = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('payments')
                        ->whereColumn('payments.order_id', 'orders.id')
                        ->whereNotNull('payments.paid_at');
                });

            if ($startDate && $endDate) {
                $query->whereBetween('orders.created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);
            }

            return $query->select('products.name', 'categories.name as category_name', DB::raw('SUM(order_items.quantity) as sold_count'))
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
        return Cache::remember("dashboard_low_stock_list_{$limit}", 300, function () use ($limit) {
            return Product::where('quantity', '<=', 5)
                ->orderBy('quantity', 'asc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Lấy số lượng sản phẩm tồn kho thấp
     */
    public function getLowStockCount()
    {
        return Cache::remember("dashboard_low_stock_count", 300, function () {
            return Product::where('quantity', '<=', 5)->count();
        });
    }

    /**
     * Lấy từ khóa tìm kiếm hàng đầu
     */
    public function getTopSearchKeywords($days = 7, $limit = 5)
    {
        return Cache::remember("dashboard_top_search_{$days}_{$limit}", 300, function () use ($days, $limit) {
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
    public function getCustomerStats($startDate = null, $endDate = null)
    {
        $cacheKey = $this->getCacheKey('dashboard_customer_stats', $startDate, $endDate);
        return Cache::remember($cacheKey, 300, function () use ($startDate, $endDate) {
            $query = User::role('user');
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);
            }
            return [
                'total' => User::role('user')->count(),
                'new_in_period' => $query->count(),
            ];
        });
    }

    /**
     * Thống kê Chat Sessions
     */
    public function getChatStats($startDate = null, $endDate = null)
    {
        $cacheKey = $this->getCacheKey('dashboard_chat_stats', $startDate, $endDate);
        return Cache::remember($cacheKey, 300, function () use ($startDate, $endDate) {
            $query = ChatSession::has('messages');
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);
            }
            return [
                'open_unassigned' => $query->count(),
            ];
        });
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

    public function clearCache()
    {
        // Đã gỡ bỏ logic xoá cache toàn trang theo yêu cầu.
    }
}
