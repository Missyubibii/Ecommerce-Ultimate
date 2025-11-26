@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->order_number)

@section('content')
    <div class="bg-gray-50 py-10 min-h-screen">
        <div class="max-w-5xl mx-auto px-4">

            <div class="mb-6 flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('customer.orders.index') }}" class="hover:text-indigo-600">Đơn hàng của tôi</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span>#{{ $order->order_number }}</span>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left: Items & Status --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Status Card --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4
                        @if($order->status == 'completed') border-green-500
                        @elseif($order->status == 'cancelled') border-red-500
                        @else border-yellow-500 @endif">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-500 uppercase tracking-wider">Trạng thái đơn hàng</p>
                                <h2 class="text-xl font-bold mt-1
                                    @if($order->status == 'completed') text-green-700
                                    @elseif($order->status == 'cancelled') text-red-700
                                    @else text-yellow-700 @endif">
                                    {{ ucfirst($order->status) }}
                                </h2>
                                <p class="text-sm text-gray-500 mt-1">Đặt ngày {{ $order->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            @if($order->status == 'pending')
                                <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST"
                                    onsubmit="return confirm('Hủy đơn hàng?')">
                                    @csrf @method('PATCH')
                                    <button
                                        class="text-red-600 border border-red-200 px-4 py-2 rounded hover:bg-red-50 transition text-sm">
                                        Hủy đơn hàng
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Items List --}}
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="font-bold text-gray-800">Sản phẩm</h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <div class="p-4 flex gap-4">
                                    <div class="w-20 h-20 border rounded bg-gray-100 flex-shrink-0 overflow-hidden">
                                        <img src="{{ $item->product_snapshot['image'] ?? 'https://placehold.co/100' }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-800">{{ $item->product_name }}</h4>
                                        <p class="text-sm text-gray-500 mt-1">SKU: {{ $item->product_snapshot['sku'] ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold">{{ number_format($item->subtotal, 0, ',', '.') }}đ</p>
                                        <p class="text-sm text-gray-500">{{ number_format($item->unit_price, 0, ',', '.') }}đ x
                                            {{ $item->quantity }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Right: Info & Payment --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- Payment Summary --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <h3 class="font-bold text-gray-800 mb-4">Thanh toán</h3>
                        <div class="space-y-3 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Tạm tính</span>
                                <span>{{ number_format($order->total_amount - $order->shipping_amount, 0, ',', '.') }}đ</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Phí vận chuyển</span>
                                <span>{{ number_format($order->shipping_amount, 0, ',', '.') }}đ</span>
                            </div>
                            <div class="border-t pt-3 flex justify-between text-base font-bold text-gray-900">
                                <span>Tổng cộng</span>
                                <span class="text-indigo-600">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t text-sm">
                            <p class="text-gray-500">Phương thức: <span
                                    class="font-medium text-gray-900 uppercase">{{ $order->payment_method }}</span></p>
                            <p class="text-gray-500 mt-1">Trạng thái thanh toán:
                                <span
                                    class="font-bold {{ $order->payment && $order->payment->status == 'paid' ? 'text-green-600' : 'text-yellow-600' }}">
                                    {{ $order->payment ? ucfirst($order->payment->status) : 'Pending' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    {{-- Address Info --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <h3 class="font-bold text-gray-800 mb-4">Thông tin giao hàng</h3>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p class="font-bold text-gray-900">{{ $order->shipping_address['full_name'] ?? 'N/A' }}</p>
                            <p>{{ $order->shipping_address['phone'] ?? 'N/A' }}</p>
                            <p class="mt-2">{{ $order->shipping_address['address_line1'] ?? '' }}</p>
                            <p>{{ $order->shipping_address['city'] ?? '' }} - {{ $order->shipping_address['state'] ?? '' }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('customer.orders.index') }}"
                            class="block w-full py-2 text-center border border-gray-300 rounded-lg bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition">
                            Quay lại danh sách đơn hàng
                        </a>
                        <a href="{{ route('home') }}"
                            class="block w-full py-2 text-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                            Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
