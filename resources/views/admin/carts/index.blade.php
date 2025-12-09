@extends('layouts.admin')
@section('title', 'Quản lý Giỏ hàng')
@section('header', 'Quản lý Giỏ hàng')

@section('content')
    <div class="p-6 bg-white rounded-xl shadow-lg">
            {{-- Header & Stats --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div class="flex justify-between items-end">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Quản lý Giỏ hàng</h1>
                        <p class="text-gray-500 text-sm mt-1">Theo dõi các giỏ hàng chưa thanh toán.</p>
                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tổng giỏ hàng</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ $carts->total() }}</h3>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Phân loại</p>
                            <div class="flex gap-2 text-xs font-bold mt-1">
                                <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded">Đã đăng nhập</span>
                                <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded">Chưa đăng nhập</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4">
                        <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Cập nhật gần nhất</p>
                            <h3 class="text-sm font-bold text-gray-800">Vừa xong</h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Table Card --}}
            <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Khách hàng</th>
                                <th class="px-6 py-4 text-center font-semibold">Sản phẩm</th>
                                <th class="px-6 py-4 text-center font-semibold">Tổng số lượng</th>
                                <th class="px-6 py-4 font-semibold">Hoạt động cuối</th>
                                <th class="px-6 py-4 text-right font-semibold">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($carts as $cart)
                                <tr class="group hover:bg-gray-50 transition-colors">
                                    {{-- Cột Khách hàng --}}
                                    <td class="px-6 py-4">
                                        @if($cart->user)
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm">
                                                    {{ substr($cart->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-800">{{ $cart->user->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $cart->user->email }}</div>
                                                </div>
                                                <span
                                                    class="ml-2 px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 uppercase tracking-wide">Đã đăng nhập</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded border border-gray-200"
                                                        title="{{ $cart->session_id }}">
                                                        {{ \Illuminate\Support\Str::limit($cart->session_id, 12) }}...
                                                    </div>
                                                    <div class="text-xs text-gray-400 mt-0.5">Khách chưa đăng nhập</div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Cột Số loại SP --}}
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 border border-gray-200 text-gray-700 font-bold">
                                            {{ $cart->total_unique_items }}
                                        </span>
                                    </td>

                                    {{-- Cột Tổng số lượng --}}
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-indigo-600 font-bold text-base">
                                            {{ $cart->total_quantity }}
                                        </span>
                                    </td>

                                    {{-- Cột Thời gian --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 text-gray-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>{{ \Carbon\Carbon::parse($cart->last_active)->diffForHumans() }}</span>
                                        </div>
                                    </td>

                                    {{-- Cột hành động --}}
                                    <td class="px-6 py-4 text-right">
                                        <div
                                            class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ route('admin.carts.show', ['user_id' => $cart->user_id, 'session_id' => $cart->session_id]) }}"
                                                class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                                title="Xem chi tiết">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                            </a>

                                            <form action="{{ route('admin.carts.destroy') }}" method="POST"
                                                onsubmit="return confirm('Bạn chắc chắn muốn xóa giỏ hàng này? Hành động này không thể hoàn tác.')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="user_id" value="{{ $cart->user_id }}">
                                                <input type="hidden" name="session_id" value="{{ $cart->session_id }}">
                                                <button type="submit"
                                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Xóa giỏ hàng">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div
                                                class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <h3 class="text-gray-800 font-bold text-lg">Danh sách trống</h3>
                                            <p class="text-gray-500 text-sm mt-1">Hiện không có giỏ hàng nào đang hoạt động.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Phân trang --}}
                @if($carts->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $carts->links() }}
                    </div>
                @endif
            </div>
    </div>
@endsection
