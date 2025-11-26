@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
            {{-- Header thông báo thành công --}}
            <div class="p-8 text-center border-b border-gray-100">
                <div
                    class="w-20 h-20 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Đặt hàng thành công!</h1>
                <p class="text-gray-600">Cảm ơn bạn đã mua sắm. Mã đơn hàng của bạn là:</p>
                <div
                    class="mt-4 inline-block bg-gray-100 px-4 py-2 rounded-md font-mono text-xl font-bold text-gray-800 border border-gray-300">
                    {{ $order->order_number }}
                </div>
            </div>

            {{-- Chi tiết đơn hàng --}}
            <div class="p-8">
                <h2 class="text-lg font-bold mb-4">Chi tiết đơn hàng</h2>

                <div class="space-y-4 mb-6">
                    @foreach($order->items as $item)
                        <div class="flex justify-between items-start">
                            <div class="flex items-center">
                                <span
                                    class="bg-gray-100 w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold mr-3">
                                    {{ $item->quantity }}x
                                </span>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $item->product_name }}</p>
                                    <p class="text-sm text-gray-500">{{ number_format($item->unit_price, 0, ',', '.') }}đ</p>
                                </div>
                            </div>
                            <span class="font-bold text-gray-700">
                                {{ number_format($item->subtotal, 0, ',', '.') }}đ
                            </span>
                        </div>
                    @endforeach
                </div>

                {{-- Tổng kết tài chính --}}
                <div class="border-t border-gray-200 pt-4 space-y-2">
                    <div class="flex justify-between text-gray-600">
                        <span>Phương thức thanh toán:</span>
                        <span class="uppercase font-medium">{{ $order->payment_method }}</span>
                    </div>
                    {{-- Nếu có shipping fee thì hiển thị ở đây --}}
                    @if($order->shipping_amount > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>Phí vận chuyển:</span>
                            <span>{{ number_format($order->shipping_amount, 0, ',', '.') }}đ</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-xl font-bold text-red-600 pt-2">
                        <span>Tổng thanh toán:</span>
                        <span>{{ number_format($order->total_amount + $order->shipping_amount, 0, ',', '.') }}đ</span>
                    </div>
                </div>

                {{-- Điều hướng --}}
                <div class="mt-8 flex justify-center gap-4">
                    <a href="{{ route('home') }}"
                        class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition">
                        Về trang chủ
                    </a>
                    @auth
                        {{-- Link này cần trỏ về route quản lý đơn hàng của user (Module A update sau) --}}
                        <a href="{{ url('/home') }}"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            Quản lý đơn hàng
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
@endsection
