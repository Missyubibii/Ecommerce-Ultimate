@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="bg-gray-50 py-10 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-6">

            {{-- Sidebar Menu --}}
            <div class="w-full md:w-1/4">
                <div class="bg-white rounded-xl shadow-sm p-4 sticky top-24">
                    <div class="flex items-center gap-3 mb-6 p-2">
                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=6366f1&color=fff" class="w-12 h-12 rounded-full">
                        <div>
                            <p class="text-xs text-gray-500">Tài khoản của</p>
                            <p class="font-bold text-gray-900">{{ Auth::user()->name }}</p>
                        </div>
                    </div>
                    <nav class="space-y-1">
                        <a href="{{ route('customer.orders.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium bg-indigo-50 text-indigo-700 rounded-lg">
                            <i data-lucide="package" class="w-4 h-4"></i>
                            Đơn mua
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg">
                            <i data-lucide="user" class="w-4 h-4"></i>
                            Tài khoản
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                Đăng xuất
                            </button>
                        </form>
                    </nav>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="flex-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h1 class="text-xl font-bold text-gray-900">Đơn hàng của tôi</h1>
                    </div>

                    @if($orders->count() > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($orders as $order)
                                <div class="p-6 hover:bg-gray-50 transition">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <div class="flex items-center gap-3">
                                                <span class="font-mono font-bold text-indigo-600">#{{ $order->order_number }}</span>
                                                <span class="text-xs text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="mt-1 text-sm text-gray-600">
                                                {{ $order->items->count() }} sản phẩm
                                            </div>
                                        </div>

                                        <span @class([
                                            'px-3 py-1 rounded-full text-xs font-bold',
                                            'bg-green-100 text-green-700' => $order->status == 'completed',
                                            'bg-red-100 text-red-700' => $order->status == 'cancelled',
                                            'bg-yellow-100 text-yellow-800' => !in_array($order->status, ['completed', 'cancelled']),
                                        ])>
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>

                                    {{-- Preview Items --}}
                                    <div class="space-y-2 mb-4">
                                        @foreach($order->items->take(2) as $item)
                                            <div class="flex gap-3 text-sm">
                                                <div class="w-10 h-10 bg-gray-100 rounded border flex-shrink-0 overflow-hidden">
                                                    <img src="{{ !empty($item->product_snapshot['image']) ? asset('storage/' . $item->product_snapshot['image']) : 'https://placehold.co/50' }}"
                                                        class="w-full h-full object-cover">
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium text-gray-800 truncate">{{ $item->product_name }}</p>
                                                    <p class="text-gray-500">x{{ $item->quantity }}</p>
                                                </div>
                                                <div class="font-medium">{{ number_format($item->subtotal, 0, ',', '.') }}đ</div>
                                            </div>
                                        @endforeach
                                        @if($order->items->count() > 2)
                                            <p class="text-xs text-gray-500 pl-14">+ {{ $order->items->count() - 2 }} sản phẩm khác</p>
                                        @endif
                                    </div>

                                    <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                                        <div class="text-sm">
                                            Tổng tiền: <span class="text-lg font-bold text-red-600">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('customer.orders.show', $order->id) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                                Xem chi tiết
                                            </a>

                                            @if($order->status == 'pending')
                                                <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn này?')">
                                                    @csrf
                                                    <button class="px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition">
                                                        Hủy đơn
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="p-6">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <i data-lucide="shopping-bag" class="w-8 h-8 text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Chưa có đơn hàng nào</h3>
                            <a href="{{ route('home') }}" class="mt-4 inline-block text-indigo-600 hover:underline">Mua sắm ngay</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
