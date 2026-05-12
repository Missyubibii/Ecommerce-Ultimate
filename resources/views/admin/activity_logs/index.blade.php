@extends('layouts.admin')

@section('title', 'Nhật ký Hoạt động')
@section('header', 'Nhật ký hệ thống')

@php
    $initialData = [
        'search' => request('subject_type', ''),
        'eventFilter' => request('event', ''),
        'type' => request('type', ''),
    ];
@endphp

@section('content')
    <div x-data="logIndexPage(@js($initialData))" class="p-6 bg-white rounded-xl shadow-lg">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nhật ký Hoạt động</h1>
                <div class="mt-2 flex items-center">
                    <div class="w-3 h-3 bg-gray-500 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-600">Tổng số bản ghi: {{ $activities->total() }}</span>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex border-b border-gray-200 mb-6">
            <button @click="changeType('')" 
                :class="type === '' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all">
                Tất cả hoạt động
            </button>
            <button @click="changeType('admin')" 
                :class="type === 'admin' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all">
                Hoạt động Admin
            </button>
            <button @click="changeType('customer')" 
                :class="type === 'customer' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all">
                Hoạt động Khách hàng
            </button>
        </div>

        {{-- Filters --}}
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex flex-wrap items-center gap-4">
                {{-- Search Subject --}}
                <div class="flex-1 min-w-[200px]">
                    <input type="text" x-model="search" @keyup.enter="applyFilters()"
                        placeholder="Tìm đối tượng (VD: App\Models\Product)..."
                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 pl-3">
                </div>

                {{-- Event Filter --}}
                <div class="min-w-[150px]">
                    <select x-model="eventFilter" @change="applyFilters()"
                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Hành động --</option>
                        <option value="created">Thêm mới</option>
                        <option value="updated">Cập nhật</option>
                        <option value="deleted">Xóa</option>
                        <option value="login">Đăng nhập</option>
                    </select>
                </div>

                <button @click="applyFilters()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300 shadow-md shadow-indigo-100">
                    Lọc dữ liệu
                </button>
                <a href="{{ route('admin.activity_logs.index') }}"
                    class="text-gray-500 hover:text-gray-700 text-sm underline">Làm lại</a>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left font-bold text-gray-600 uppercase tracking-wider">Thời gian</th>
                            <th class="px-6 py-4 text-left font-bold text-gray-600 uppercase tracking-wider">Hành động & Mô tả</th>
                            <th class="px-6 py-4 text-left font-bold text-gray-600 uppercase tracking-wider">Đối tượng</th>
                            <th class="px-6 py-4 text-left font-bold text-gray-600 uppercase tracking-wider">Người thực hiện</th>
                            <th class="px-6 py-4 text-right font-bold text-gray-600 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($activities as $log)
                            @php
                                $eventLabels = [
                                    'created' => 'THÊM MỚI',
                                    'updated' => 'CẬP NHẬT',
                                    'deleted' => 'XÓA',
                                    'login' => 'ĐĂNG NHẬP',
                                    'sync' => 'ĐỒNG BỘ'
                                ];
                                
                                // Dịch mô tả nếu chứa các cụm từ tiếng Anh
                                $desc = $log->description;
                                $translations = [
                                    'Order has been' => 'Đơn hàng đã được',
                                    'User has been' => 'Người dùng đã được',
                                    'Product has been' => 'Sản phẩm đã được',
                                    'Category has been' => 'Danh mục đã được',
                                    'Brand has been' => 'Thương hiệu đã được',
                                    'Payment has been' => 'Thanh toán đã được',
                                    'Shipment has been' => 'Vận chuyển đã được',
                                    'created' => 'tạo',
                                    'updated' => 'cập nhật',
                                    'deleted' => 'xóa',
                                    'login' => 'đăng nhập'
                                ];
                                $desc = str_replace(array_keys($translations), array_values($translations), $desc);
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium">
                                    {{ $log->created_at->format('H:i d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold tracking-wider
                                            {{ $log->event == 'created' ? 'bg-emerald-100 text-emerald-700' :
                                                ($log->event == 'updated' ? 'bg-amber-100 text-amber-700' :
                                                    ($log->event == 'deleted' ? 'bg-red-100 text-red-700' : 'bg-indigo-100 text-indigo-700')) }}">
                                            {{ $eventLabels[$log->event] ?? strtoupper($log->event) }}
                                        </span>
                                        <span class="text-gray-700 font-medium">{{ $desc }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-gray-900">{{ class_basename($log->subject_type) }}</span>
                                        <span class="text-[10px] font-mono text-gray-400">ID: #{{ $log->subject_id }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-600">
                                            {{ substr($log->causer ? $log->causer->name : 'H', 0, 1) }}
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700">{{ $log->causer ? $log->causer->name : 'Hệ thống/Khách' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs font-bold text-indigo-600 hover:bg-gray-50 transition-all shadow-sm"
                                        onclick="alert('Dữ liệu thay đổi: {{ json_encode($log->properties) }}')">
                                        Chi tiết
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p class="text-sm font-medium">Chưa có nhật ký hoạt động nào được ghi lại.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8">
            {{ $activities->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('logIndexPage', (init) => ({
                search: init.search,
                eventFilter: init.eventFilter,
                type: init.type,
                applyFilters() {
                    let params = new URLSearchParams(window.location.search);
                    if (this.search) params.set('subject_type', this.search); else params.delete('subject_type');
                    if (this.eventFilter) params.set('event', this.eventFilter); else params.delete('event');
                    if (this.type) params.set('type', this.type); else params.delete('type');
                    window.location.search = params.toString();
                },
                changeType(newType) {
                    this.type = newType;
                    this.applyFilters();
                }
            }));
        });
    </script>
@endsection
