@extends('layouts.admin')

@section('title', 'Bảng điều khiển')
@section('header', 'Tổng quan hệ thống')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 1. KPI Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Doanh thu -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100 text-indigo-500">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-gray-600">Tổng doanh thu</p>
                            <p class="text-lg font-semibold text-gray-700">
                                {{ number_format($stats['total_revenue'], 0, ',', '.') }}đ</p>
                        </div>
                    </div>
                </div>

                <!-- Đơn hàng mới -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-gray-600">Đơn chờ xử lý</p>
                            <p class="text-lg font-semibold text-gray-700">{{ $stats['pending_orders'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Khách hàng -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-500">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-gray-600">Khách hàng</p>
                            <p class="text-lg font-semibold text-gray-700">{{ $stats['total_customers'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sản phẩm sắp hết -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-500">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-gray-600">Sắp hết hàng</p>
                            <p class="text-lg font-semibold text-gray-700">{{ $stats['low_stock_products'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Charts & Recent Orders --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Biểu đồ doanh thu -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Biểu đồ doanh thu (7 ngày)</h3>
                    <div class="relative h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Đơn hàng mới nhất -->
                <div class="lg:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Đơn hàng mới</h3>
                        <a href="{{ route('admin.orders.index') }}"
                            class="text-sm text-indigo-600 hover:text-indigo-900">Xem tất cả &rarr;</a>
                    </div>
                    <div class="overflow-y-auto max-h-64">
                        <ul class="divide-y divide-gray-200">
                            @foreach($recentOrders as $order)
                                                <li class="py-3">
                                                    <div class="flex justify-between">
                                                        <div>
                                                            <a href="{{ route('admin.orders.show', $order->id) }}"
                                                                class="text-sm font-medium text-indigo-600 hover:underline">
                                                                #{{ $order->order_number }}
                                                            </a>
                                                            <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                                                        </div>
                                                        <div class="text-right">
                                                            <p class="text-sm font-bold text-gray-900">
                                                                {{ number_format($order->total_amount) }}đ</p>
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' :
                                ($order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                                {{ ucfirst($order->status) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: @json($chartData['values']),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
@endsection
