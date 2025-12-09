@extends('layouts.admin')

@section('title', 'Quản lý Đơn hàng')
@section('header', 'Danh sách Đơn hàng')

@php
    // Chuẩn bị dữ liệu cho Alpine
    $initialData = [
        'search' => request('q', ''),
        'statusFilter' => request('status', ''),
        'dateFrom' => request('date_from', ''),
    ];
@endphp

@section('content')
    <div x-data="orderIndexPage(@js($initialData))" class="p-6 bg-white rounded-xl shadow-lg">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quản lý Đơn hàng</h1>
                <div class="mt-2 flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-600">Tổng đơn: {{ $orders->total() }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters Bar --}}
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex flex-wrap items-center gap-4">
                {{-- Search --}}
                <div class="flex-1 min-w-[200px]">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" x-model="search" @keyup.enter="applyFilters()"
                            placeholder="Mã đơn, Khách hàng..."
                            class="pl-10 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                {{-- Status Filter --}}
                <div class="min-w-[180px]">
                    <select x-model="statusFilter" @change="applyFilters()"
                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Tất cả trạng thái --</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Date Filter --}}
                <div class="min-w-[150px]">
                    <input type="date" x-model="dateFrom" @change="applyFilters()"
                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        title="Từ ngày">
                </div>

                {{-- Action Buttons --}}
                <button @click="applyFilters()"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300">
                    Lọc
                </button>
                <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:text-gray-700 text-sm underline">Xóa
                    lọc</a>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Mã đơn</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Khách hàng</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Trạng thái</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Thanh toán</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Vận chuyển</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">Tổng tiền</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">Ngày đặt</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                        class="font-bold text-indigo-600 hover:underline">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $order->user ? $order->user->name : 'Khách vãng lai' }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->shipping_address['full_name'] ?? '' }}</div>
                                </td>

                                {{-- Status Badges (Đồng bộ style) --}}
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $stClass = match ($order->status) {
                                            'completed', 'shipped' => 'bg-green-100 text-green-800',
                                            'cancelled', 'refunded' => 'bg-red-100 text-red-800',
                                            'processing' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-yellow-100 text-yellow-800'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $stClass }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if($order->payment)
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->payment->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst($order->payment->status) }}
                                        </span>
                                        <div class="text-[10px] uppercase text-gray-400 mt-1">{{ $order->payment->method }}</div>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if($order->shipment)
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->shipment->status == 'delivered' ? 'bg-green-100 text-green-800' : 'bg-blue-50 text-blue-600' }}">
                                            {{ ucfirst($order->shipment->status) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-right font-bold text-gray-800">
                                    {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                </td>

                                <td class="px-4 py-3 text-right text-xs text-gray-500">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900 font-medium text-xs uppercase tracking-wider">
                                        Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <p class="text-sm font-medium">Không tìm thấy đơn hàng nào.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $orders->withQueryString()->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('orderIndexPage', (init) => ({
                search: init.search,
                statusFilter: init.statusFilter,
                dateFrom: init.dateFrom,

                applyFilters() {
                    let params = new URLSearchParams(window.location.search);

                    if (this.search) params.set('q', this.search); else params.delete('q');
                    if (this.statusFilter) params.set('status', this.statusFilter); else params.delete('status');
                    if (this.dateFrom) params.set('date_from', this.dateFrom); else params.delete('date_from');

                    params.delete('page');
                    window.location.search = params.toString();
                }
            }));
        });
    </script>
@endsection
