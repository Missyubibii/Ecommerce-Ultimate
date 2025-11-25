@extends('layouts.admin')

@section('title', 'Quản lý Mã giảm giá')
@section('header', 'Danh sách Mã giảm giá')

@section('content')
    <div class="p-6 bg-white rounded-xl shadow-lg">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quản lý Mã giảm giá</h1>
                <div class="mt-2 flex items-center">
                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-600">Tổng số mã: {{ $coupons->total() }}</span>
                </div>
            </div>

            <a href="{{ route('admin.coupons.create') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center transition duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                Tạo mã mới
            </a>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div
                class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Mã (Code)</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Loại giảm</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Giá trị</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600 uppercase">Lượt dùng</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Hạn dùng</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600 uppercase">Trạng thái</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($coupons as $coupon)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded border border-indigo-100">
                                        {{ $coupon->code }}
                                    </span>
                                    @if($coupon->description)
                                        <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">{{ $coupon->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-900">
                                    {{ $coupon->type == 'percent' ? 'Theo %' : 'Số tiền cố định' }}
                                </td>
                                <td class="px-6 py-4 font-bold text-green-600">
                                    {{ $coupon->type == 'percent' ? $coupon->value . '%' : number_format($coupon->value) . 'đ' }}
                                </td>
                                <td class="px-6 py-4 text-center text-gray-600">
                                    {{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    @if($coupon->ends_at)
                                        <div class="{{ now()->gt($coupon->ends_at) ? 'text-red-500 font-bold' : '' }}">
                                            {{ $coupon->ends_at->format('d/m/Y') }}
                                        </div>
                                        @if(now()->gt($coupon->ends_at)) <span>(Hết hạn)</span> @endif
                                    @else
                                        <span class="text-gray-400 italic">Không thời hạn</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full {{ $coupon->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                                        class="text-blue-600 hover:text-blue-900 font-medium text-xs uppercase">Sửa</a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã này?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 font-medium text-xs uppercase">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <p class="text-sm font-medium">Chưa có mã giảm giá nào.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $coupons->links() }}
        </div>
    </div>
@endsection
