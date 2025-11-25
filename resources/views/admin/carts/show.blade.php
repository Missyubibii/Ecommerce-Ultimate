@extends('layouts.admin')

@section('title', 'Chi tiết giỏ hàng')

{{-- Sử dụng block section thay vì tham số thứ 2 để hiển thị biến dynamic --}}
@section('header')
    Chi tiết giỏ hàng: {{ $owner }}
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('admin.carts.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại danh sách
            </a>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            <div class="w-full lg:w-3/4 bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Danh sách sản phẩm</h3>
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">Sản phẩm</th>
                                <th class="px-5 py-3 bg-gray-50 text-center text-xs font-semibold text-gray-600 uppercase">Đơn giá</th>
                                <th class="px-5 py-3 bg-gray-50 text-center text-xs font-semibold text-gray-600 uppercase">Số lượng</th>
                                <th class="px-5 py-3 bg-gray-50 text-right text-xs font-semibold text-gray-600 uppercase">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white flex items-center gap-3">
                                        <img src="{{ $item->product->image_url }}" alt="" class="w-10 h-10 rounded object-cover border">
                                        <div>
                                            <p class="text-gray-900 font-medium">{{ $item->product->name }}</p>
                                            <p class="text-gray-500 text-xs">SKU: {{ $item->product->sku }}</p>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-center">
                                        {{ number_format($item->product->price, 0, ',', '.') }}đ
                                    </td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-center">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-right font-bold">
                                        {{ number_format($item->total, 0, ',', '.') }}đ
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="w-full lg:w-1/4">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 sticky top-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">Tóm tắt</h3>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Số lượng item:</span>
                            <span class="font-medium">{{ $items->count() }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tổng số lượng:</span>
                            <span class="font-medium">{{ $items->sum('quantity') }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-t border-b border-gray-100 mt-2">
                            <span class="text-gray-800 font-semibold">Tổng cộng:</span>
                            <span class="font-bold text-red-600 text-lg">{{ number_format($cartTotal, 0, ',', '.') }}đ</span>
                        </div>
                    </div>

                    <div>
                        <form action="{{ route('admin.carts.destroy') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa giỏ hàng này? Hành động này không thể hoàn tác.');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="user_id" value="{{ $userId }}">
                            <input type="hidden" name="session_id" value="{{ $sessionId }}">

                            <button type="submit" class="w-full bg-red-50 text-red-700 border border-red-200 py-2 px-4 rounded hover:bg-red-100 hover:border-red-300 transition flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Xóa Giỏ Hàng
                            </button>
                            <p class="text-xs text-gray-500 mt-3 text-center leading-relaxed">
                                Hành động này sẽ xóa vĩnh viễn các item trong giỏ hàng này khỏi cơ sở dữ liệu.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
