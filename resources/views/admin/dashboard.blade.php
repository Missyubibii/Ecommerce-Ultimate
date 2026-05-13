@extends('layouts.admin')

@section('title', 'Bảng điều khiển Admin')

@section('content')
    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen" 
        x-data="{
            autoRefresh: localStorage.getItem('admin_auto_refresh') === 'true',
            countdown: 30,
            maxCountdown: 30,
            isRefreshing: false,
            lastUpdated: '{{ now()->format('H:i:s') }}',
            filterType: '30_days',
            startDate: '',
            endDate: '',
            
            init() {
                this.$watch('autoRefresh', value => {
                    localStorage.setItem('admin_auto_refresh', value);
                    if (value) this.startTimer();
                });
                this.$watch('filterType', () => {
                    if (this.filterType !== 'custom') {
                        this.refreshData(true);
                    }
                });
                if (this.autoRefresh) this.startTimer();
            },
            
            startTimer() {
                let timer = setInterval(() => {
                    if (!this.autoRefresh) {
                        clearInterval(timer);
                        return;
                    }
                    if (this.countdown > 0) {
                        this.countdown--;
                    } else {
                        this.refreshData();
                        this.countdown = this.maxCountdown;
                    }
                }, 1000);
            },
            
            async refreshData(isManual = false) {
                this.isRefreshing = true;
                try {
                    let url = `{{ route('admin.dashboard') }}?realtime=1&filter_type=${this.filterType}`;
                    if (this.filterType === 'custom') {
                        if (!this.startDate || !this.endDate) {
                            this.isRefreshing = false;
                            return; // Yêu cầu nhập đủ ngày
                        }
                        url += `&start_date=${this.startDate}&end_date=${this.endDate}`;
                    }

                    const response = await fetch(url, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const result = await response.json();
                    if (result.success) {
                        // Dispatch event to components (Stats cards, Charts)
                        window.dispatchEvent(new CustomEvent('dashboard-updated', { detail: result.data }));
                        
                        // Inject HTML for tables
                        if(result.html) {
                            if(document.getElementById('recent-orders-container')) 
                                document.getElementById('recent-orders-container').innerHTML = result.html.recent_orders;
                            if(document.getElementById('low-stock-container'))
                                document.getElementById('low-stock-container').innerHTML = result.html.low_stock;
                            if(document.getElementById('activity-logs-container'))
                                document.getElementById('activity-logs-container').innerHTML = result.html.activity_logs;
                        }

                        this.lastUpdated = result.data.last_updated;
                        if(isManual) {
                            window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Dữ liệu đã được cập nhật!', type: 'success' } }));
                        }
                    }
                } catch (error) {
                    console.error('Failed to refresh dashboard:', error);
                } finally {
                    this.isRefreshing = false;
                    if(isManual) this.countdown = this.maxCountdown;
                }
            }
        }">
        
        <!-- Header with Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tổng quan hệ thống</h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-sm text-gray-500">Cập nhật lúc: <span x-text="lastUpdated" class="font-mono font-bold text-indigo-600"></span></span>
                    <span x-show="isRefreshing" x-cloak class="flex h-2 w-2 rounded-full bg-indigo-500 animate-ping"></span>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                {{-- Date Filter --}}
                <div class="flex items-center gap-2">
                    <select x-model="filterType" class="text-sm border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-8 shadow-sm">
                        <option value="1_day">Hôm nay</option>
                        <option value="7_days">7 ngày qua</option>
                        <option value="30_days">30 ngày qua</option>
                        <option value="custom">Tùy chọn...</option>
                    </select>

                    <template x-if="filterType === 'custom'">
                        <div class="flex items-center gap-2">
                            <input type="date" x-model="startDate" @change="if(startDate && endDate) refreshData(true)" class="text-sm border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 py-2 shadow-sm">
                            <span class="text-gray-500">-</span>
                            <input type="date" x-model="endDate" @change="if(startDate && endDate) refreshData(true)" class="text-sm border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 py-2 shadow-sm">
                        </div>
                    </template>
                </div>

                {{-- Auto Refresh Toggle --}}
                <div class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 rounded-xl shadow-sm">
                    <span class="text-xs font-bold text-gray-600">Tự động (30s)</span>
                    <button @click="autoRefresh = !autoRefresh" 
                        class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                        :class="autoRefresh ? 'bg-indigo-600' : 'bg-gray-200'">
                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                            :class="autoRefresh ? 'translate-x-5' : 'translate-x-0'"></span>
                    </button>
                </div>

                {{-- Countdown Circle/Progress --}}
                <div x-show="autoRefresh" x-cloak class="relative flex items-center justify-center w-10 h-10">
                    <svg class="w-10 h-10 transform -rotate-90">
                        <circle cx="20" cy="20" r="18" stroke="currentColor" stroke-width="3" fill="transparent" class="text-gray-100" />
                        <circle cx="20" cy="20" r="18" stroke="currentColor" stroke-width="3" fill="transparent" class="text-indigo-500 transition-all duration-1000"
                            :stroke-dasharray="113" :stroke-dashoffset="113 - (113 * countdown / maxCountdown)" />
                    </svg>
                    <span class="absolute text-[10px] font-bold text-gray-700" x-text="countdown"></span>
                </div>

                <button @click="refreshData(true)" :disabled="isRefreshing"
                    class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm disabled:opacity-50">
                    <svg class="w-4 h-4" :class="isRefreshing ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Làm mới
                </button>
                
                <div class="px-4 py-2 bg-indigo-600 rounded-xl text-sm font-bold text-white shadow-lg shadow-indigo-200">
                    {{ now()->format('d/m/Y') }}
                </div>
            </div>
        </div>

        <!-- Alert Messages
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                {{ session('success') }}
            </div>
        @endif -->

        <!-- KPI Stats Cards -->
        <x-dashboard.stats-cards :salesStats="$salesStats" :orderStats="$orderStats" :customerStats="$customerStats"
            :chatStats="$chatStats" :lowStockCount="$lowStockCount" />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Revenue Chart (2/3) -->
            <div class="lg:col-span-2">
                <x-dashboard.revenue-chart :revenueChart="$revenueChart" />
            </div>

            <!-- Top Products (1/3) -->
            <div class="lg:col-span-1">
                <x-dashboard.top-products :topProducts="$topProducts" />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Recent Orders (2/3) -->
            <div class="lg:col-span-2" id="recent-orders-container">
                <x-dashboard.recent-orders-table :recentOrders="$recentOrders" />
            </div>

            <!-- Low Stock (1/3) -->
            <div class="lg:col-span-1" id="low-stock-container">
                <x-dashboard.low-stock-table :lowStock="$lowStock" />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Search Keywords (1/2) -->
            <x-dashboard.search-keywords :topKeywords="$topKeywords" :searchDays="$searchDays" />

            <!-- Activity Logs (1/2) -->
            <div id="activity-logs-container">
                <x-dashboard.activity-logs :activities="$activities" />
            </div>
        </div>
    </div>

    <!-- Chart.js and Alpine.js (Optional but good to have) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Global helper for formatting currency
        window.formatCurrency = function(val) {
            if (val === null || val === undefined || isNaN(val)) return '0đ';
            return new Intl.NumberFormat('vi-VN').format(val) + 'đ';
        };
    </script>
@endsection