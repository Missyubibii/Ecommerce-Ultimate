@extends('layouts.admin')

@section('title', 'Quản lý Mã giảm giá')
@section('header')
    <div class="flex justify-between items-center">
        <span>Danh sách Mã giảm giá</span>
        <a href="{{ route('admin.coupons.create') }}"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
            + Tạo mới
        </a>
    </div>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã (Code)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giá trị</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sử dụng</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thời hạn</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái
                                </th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($coupons as $coupon)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-indigo-600">{{ $coupon->code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $coupon->type == 'percent' ? 'Phần trăm' : 'Tiền mặt' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-green-600">
                                        {{ $coupon->type == 'percent' ? $coupon->value . '%' : number_format($coupon->value) . 'đ' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        {{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        @if($coupon->ends_at)
                                            {{ $coupon->ends_at->format('d/m/Y') }}
                                            @if(now()->gt($coupon->ends_at)) <span class="text-red-500">(Hết hạn)</span> @endif
                                        @else
                                            Vô thời hạn
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $coupon->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 mr-2">Sửa</a>
                                        <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST"
                                            class="inline" onsubmit="return confirm('Xóa mã này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t">
                    {{ $coupons->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
