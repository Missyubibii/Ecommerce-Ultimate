@extends('layouts.admin')

@section('title', 'Quản lý Giỏ hàng')
@section('header', 'Giỏ hàng đang hoạt động')

@php
    $initialData = [
        'search' => request('q', ''), // Tìm theo User ID hoặc Session ID
    ];
@endphp

@section('content')
<div x-data="cartIndexPage(@js($initialData))" class="p-6 bg-white rounded-xl shadow-lg">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quản lý Giỏ hàng</h1>
            <div class="mt-2 flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-600">Tổng giỏ hàng: {{ $carts->total() }}</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-600">Khách vãng lai: {{ $carts->whereNull('user_id')->count() }}</span>
                </div>
            </div>
        </div>

        {{-- Nút xóa hàng loạt (Nếu có logic sau này) --}}
        {{-- <button ... class="bg-red-600 ...">Dọn dẹp giỏ rác</button> --}}
    </div>

    {{-- Filter Bar --}}
    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[250px]">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" x-model="search" @keyup.enter="applyFilters()" placeholder="Tìm email, session ID..."
                        class="pl-10 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <button @click="applyFilters()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300">
                Lọc
            </button>
            <a href="{{ route('admin.carts.index') }}" class="text-gray-500 hover:text-gray-700 text-sm underline">Xóa lọc</a>
        </div>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Chủ sở hữu</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 uppercase tracking-wider">Loại khách</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 uppercase tracking-wider">Số lượng SP</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase tracking-wider">Giá trị</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase tracking-wider">Cập nhật cuối</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase tracking-wider">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($carts as $cart)
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out group">
                            <td class="px-6 py-4">
                                @if($cart->user_id)
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs mr-3">
                                            {{ substr($cart->user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $cart->user->name ?? 'Unknown' }}</div>
                                            <div class="text-xs text-gray-500">{{ $cart->user->email ?? '' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 font-bold text-xs mr-3">
                                            G
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 italic">Khách vãng lai</div>
                                            <div class="text-[10px] text-gray-400 font-mono truncate w-32" title="{{ $cart->session_id }}">
                                                {{ $cart->session_id }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cart->user_id ? 'bg-indigo-100 text-indigo-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $cart->user_id ? 'Thành viên' : 'Vãng lai' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-gray-800">{{ $cart->total_quantity }}</span>
                                <span class="text-gray-500 text-xs">({{ $cart->distinct_items }} loại)</span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900">
                                {{ number_format($cart->cart_total, 0, ',', '.') }}đ
                            </td>
                            <td class="px-6 py-4 text-right text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($cart->last_updated)->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.carts.show', ['user_id' => $cart->user_id, 'session_id' => $cart->session_id]) }}"
                                        class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors text-xs uppercase font-bold">
                                        Chi tiết
                                    </a>
                                    {{-- Nút xóa nhanh nếu cần --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <p class="text-lg font-medium text-gray-900">Không có giỏ hàng nào</p>
                                    <p class="text-sm text-gray-500 mt-1">Hiện tại không có khách hàng nào đang giữ hàng trong giỏ.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $carts->withQueryString()->links() }}
    </div>
</div>

{{-- Alpine Script --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('cartIndexPage', (init) => ({
            search: init.search,
            applyFilters() {
                let params = new URLSearchParams(window.location.search);
                if (this.search) params.set('q', this.search); else params.delete('q');
                params.delete('page');
                window.location.search = params.toString();
            }
        }));
    });
</script>
@endsection
