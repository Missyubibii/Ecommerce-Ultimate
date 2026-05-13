@props(['salesStats', 'orderStats', 'customerStats', 'chatStats', 'lowStockCount'])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"
    x-data="{
        stats: @js($salesStats),
        orders: @js($orderStats),
        customers: @js($customerStats),
        chat: @js($chatStats),
        lowStock: @js($lowStockCount ?? 0)
    }"
    @dashboard-updated.window="
        stats = $event.detail.salesStats;
        orders = $event.detail.orderStats;
        customers = $event.detail.customerStats;
        chat = $event.detail.chatStats;
        lowStock = $event.detail.lowStockCount;
    ">
    
    <!-- 1. Revenue -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 rounded-xl bg-indigo-50 text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <template x-if="stats.revenue_today_change !== 'N/A'">
                <span class="flex items-center text-xs font-medium" :class="stats.revenue_today_change >= 0 ? 'text-green-600' : 'text-red-600'">
                    <template x-if="stats.revenue_today_change >= 0">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 10l7-7 7 7M12 3v18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </template>
                    <template x-if="stats.revenue_today_change < 0">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 14l-7 7-7-7M12 21V3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </template>
                    <span x-text="Math.abs(stats.revenue_today_change).toFixed(1) + '%'"></span>
                </span>
            </template>
            <template x-if="stats.revenue_today_change === 'N/A'">
                <span class="text-xs font-medium text-gray-400">N/A</span>
            </template>
        </div>
        <p class="text-sm font-medium text-gray-500 mb-1" x-text="filterType === '1_day' ? 'Doanh thu (Hôm nay)' : (filterType === '7_days' ? 'Doanh thu (7 ngày qua)' : (filterType === '30_days' ? 'Doanh thu (30 ngày qua)' : 'Doanh thu (Tùy chọn)'))">Doanh thu</p>
        <h3 class="text-2xl font-bold text-gray-900" x-text="formatCurrency(stats.revenue_today)"></h3>
    </div>

    <!-- 2. Orders -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 rounded-xl bg-amber-50 text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <div class="flex flex-col gap-1 text-right">
                <span class="text-xs font-medium text-gray-500">Hoàn tất: <span class="font-bold text-green-600" x-text="stats.completed_orders_count"></span></span>
            </div>
        </div>
        <div class="flex justify-between items-end">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Đơn hàng (kỳ chọn)</p>
                <div class="flex gap-2">
                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-gray-100 text-gray-700">Mới: <span x-text="orders.pending"></span></span>
                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-blue-100 text-blue-700">Xử lý: <span x-text="orders.processing"></span></span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Customers & Chat -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 rounded-xl bg-blue-50 text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="flex gap-2">
                <span class="flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-700">
                    Khách mới: <span x-text="customers.new_in_period"></span>
                </span>
            </div>
        </div>
        <p class="text-sm font-medium text-gray-500 mb-1">Tương tác (kỳ chọn)</p>
        <div class="flex items-baseline gap-2">
            <h3 class="text-xl font-bold text-gray-900"><span x-text="chat.open_unassigned"></span> Chat hỗ trợ</h3>
            <span x-show="chat.open_unassigned > 0" x-cloak class="animate-pulse flex h-2 w-2 rounded-full bg-red-500"></span>
        </div>
    </div>

    <!-- 4. AOV & Low Stock -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex gap-2">
                <span class="flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-red-100 text-red-700" title="Sản phẩm tồn kho thấp (<= 5)">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span x-text="lowStock"></span> SP cạn
                </span>
            </div>
        </div>
        <p class="text-sm font-medium text-gray-500 mb-1">AOV (Trung bình/đơn)</p>
        <div class="flex items-baseline gap-2">
            <h3 class="text-2xl font-bold text-gray-900" x-text="formatCurrency(stats.aov)"></h3>
        </div>
    </div>
</div>
