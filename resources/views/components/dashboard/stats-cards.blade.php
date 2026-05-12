@props(['salesStats', 'orderStats', 'customerStats', 'chatStats'])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"
    x-data="{
        stats: @json($salesStats),
        orders: @json($orderStats),
        customers: @json($customerStats),
        chat: @json($chatStats),
        formatCurrency(val) {
            return new Intl.NumberFormat('vi-VN').format(val) + 'đ';
        }
    }"
    @dashboard-updated.window="
        stats = $event.detail.salesStats;
        orders = $event.detail.orderStats;
        customers = $event.detail.customerStats;
        chat = $event.detail.chatStats;
    ">
    <!-- Revenue Today -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 rounded-xl bg-indigo-50 text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <span class="flex items-center text-xs font-medium" :class="stats.revenue_today_change >= 0 ? 'text-green-600' : 'text-red-600'">
                <template x-if="stats.revenue_today_change >= 0">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 10l7-7 7 7M12 3v18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </template>
                <template x-if="stats.revenue_today_change < 0">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 14l-7 7-7-7M12 21V3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </template>
                <span x-text="Math.abs(stats.revenue_today_change).toFixed(1) + '%'"></span>
            </span>
        </div>
        <p class="text-sm font-medium text-gray-500 mb-1" x-text="filterType === '1_day' ? 'Doanh thu (Hôm nay)' : (filterType === '7_days' ? 'Doanh thu (7 ngày qua)' : (filterType === 'month' ? 'Doanh thu (Tháng này)' : (filterType === 'custom' ? 'Doanh thu (Tùy chọn)' : 'Doanh thu hôm nay')))">Doanh thu hôm nay</p>
        <h3 class="text-2xl font-bold text-gray-900" x-text="formatCurrency(stats.revenue_today)"></h3>
    </div>

    <!-- Monthly Revenue -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 rounded-xl bg-blue-50 text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <span class="flex items-center text-xs font-medium" :class="stats.revenue_month_change >= 0 ? 'text-green-600' : 'text-red-600'">
                <span x-text="Math.abs(stats.revenue_month_change).toFixed(1) + '%'"></span>
            </span>
        </div>
        <p class="text-sm font-medium text-gray-500 mb-1" x-text="filterType === 'default' ? 'Doanh thu tháng này' : 'Doanh thu (Tháng tương ứng)'">Doanh thu tháng này</p>
        <h3 class="text-2xl font-bold text-gray-900" x-text="formatCurrency(stats.revenue_month)"></h3>
    </div>

    <!-- Orders Overview -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 rounded-xl bg-amber-50 text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <div class="flex gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">Mới: <span x-text="orders.pending"></span></span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">XL: <span x-text="orders.processing"></span></span>
            </div>
        </div>
        <p class="text-sm font-medium text-gray-500 mb-1" x-text="filterType === 'default' ? 'Đơn hàng mới & Đang xử lý' : 'Đơn mới & Đang xử lý (kỳ chọn)'">Đơn hàng mới & Đang xử lý</p>
        <h3 class="text-2xl font-bold text-gray-900" x-text="parseInt(orders.pending) + parseInt(orders.processing)"></h3>
    </div>

    <!-- System Info (AOV & Chat) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
            </div>
            <span x-show="chat.open_unassigned > 0" x-cloak class="animate-pulse flex h-2 w-2 rounded-full bg-red-500"></span>
        </div>
        <p class="text-sm font-medium text-gray-500 mb-1" x-text="filterType === 'default' ? 'AOV / Chat chưa rep' : 'AOV / Chat (kỳ chọn)'">AOV / Chat chưa rep</p>
        <div class="flex items-baseline gap-2">
            <h3 class="text-xl font-bold text-gray-900" x-text="formatCurrency(stats.aov)"></h3>
            <span class="text-sm text-red-500 font-bold" x-show="chat.open_unassigned > 0">
                (<span x-text="chat.open_unassigned"></span> chat)
            </span>
        </div>
    </div>
</div>
