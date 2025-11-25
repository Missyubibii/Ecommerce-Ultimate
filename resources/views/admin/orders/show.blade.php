@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->order_number)
@section('header')
    <div class="flex justify-between items-center">
        <span>Đơn hàng <span class="text-indigo-600 font-mono">#{{ $order->order_number }}</span></span>
    </div>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Thông báo Flash --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- CỘT TRÁI: DANH SÁCH SẢN PHẨM & QUẢN LÝ VẬN CHUYỂN --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- 1. Danh sách sản phẩm (Items) --}}
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div
                            class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Sản phẩm ({{ $order->items->count() }})
                            </h3>
                            <span class="text-sm text-gray-500">Ngày đặt:
                                {{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <ul class="divide-y divide-gray-200">
                            @foreach($order->items as $item)
                                <li class="p-4 flex items-center hover:bg-gray-50">
                                    <div class="flex-shrink-0 h-16 w-16 border rounded overflow-hidden bg-white">
                                        <img class="h-full w-full object-contain"
                                            src="{{ $item->product_snapshot['image'] ?? 'https://placehold.co/100' }}" alt="">
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="flex justify-between">
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-900">{{ $item->product_name }}</h4>
                                                <p class="text-xs text-gray-500">SKU:
                                                    {{ $item->product_snapshot['sku'] ?? 'N/A' }}</p>
                                            </div>
                                            <p class="text-sm font-bold text-gray-900">
                                                {{ number_format($item->subtotal, 0, ',', '.') }}đ</p>
                                        </div>
                                        <div class="flex justify-between mt-2 text-sm text-gray-500">
                                            <span>{{ number_format($item->unit_price, 0, ',', '.') }}đ x
                                                {{ $item->quantity }}</span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="bg-gray-50 px-4 py-4 sm:px-6 border-t border-gray-200">
                            <div class="flex flex-col gap-2">
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Tạm tính:</span>
                                    <span>{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Phí vận chuyển:</span>
                                    <span>{{ number_format($order->shipping_amount ?? 0, 0, ',', '.') }}đ</span>
                                </div>
                                <div
                                    class="flex justify-between text-xl font-bold text-indigo-600 border-t border-gray-200 pt-2 mt-2">
                                    <span>Tổng cộng:</span>
                                    <span>{{ number_format(($order->total_amount + ($order->shipping_amount ?? 0)), 0, ',', '.') }}đ</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Quản lý Vận chuyển (Module H - Shipments) --}}
                    <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-blue-100">
                        <div class="px-4 py-4 bg-blue-50 border-b border-blue-100 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0">
                                </path>
                            </svg>
                            <h3 class="text-lg font-medium text-blue-800">Thông tin Vận chuyển (Shipment)</h3>
                        </div>

                        @if($order->shipment)
                            <div class="p-6">
                                <form action="{{ route('admin.orders.update_shipment', $order->shipment->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Đơn vị vận chuyển</label>
                                            <input type="text" name="carrier" value="{{ $order->shipment->carrier }}"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                placeholder="VD: Giao Hàng Tiết Kiệm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Mã vận đơn (Tracking)</label>
                                            <div class="mt-1 flex rounded-md shadow-sm">
                                                <input type="text" name="tracking_number"
                                                    value="{{ $order->shipment->tracking_number }}"
                                                    class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                    placeholder="Nhập mã tracking...">
                                                <span
                                                    class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                        </path>
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-span-1 md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700">Trạng thái giao hàng</label>
                                            <select name="status"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                                @foreach(['pending' => 'Chờ lấy hàng', 'picked_up' => 'Đã lấy hàng', 'in_transit' => 'Đang giao', 'delivered' => 'Đã giao', 'returned' => 'Hoàn trả', 'failed' => 'Giao thất bại'] as $key => $label)
                                                    <option value="{{ $key }}" {{ $order->shipment->status == $key ? 'selected' : '' }}>{{ $label }} ({{ $key }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex justify-end">
                                        <button type="submit"
                                            class="bg-blue-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-blue-700 focus:outline-none">
                                            Cập nhật Vận chuyển
                                        </button>
                                    </div>
                                </form>

                                {{-- Shipment Timestamps --}}
                                @if($order->shipment->shipped_at || $order->shipment->delivered_at)
                                    <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-500 flex gap-4">
                                        @if($order->shipment->shipped_at)
                                            <span><span class="font-bold">Đã gửi:</span>
                                                {{ $order->shipment->shipped_at->format('d/m/Y H:i') }}</span>
                                        @endif
                                        @if($order->shipment->delivered_at)
                                            <span><span class="font-bold">Đã giao:</span>
                                                {{ $order->shipment->delivered_at->format('d/m/Y H:i') }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="p-6 text-center text-gray-500 italic">
                                Không có thông tin vận chuyển cho đơn hàng này.
                            </div>
                        @endif
                    </div>

                    {{-- Thông tin Khách hàng & Địa chỉ --}}
                    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                        <div class="px-4 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-medium text-gray-900">Thông tin Giao nhận</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-bold text-gray-700 uppercase mb-2">Người nhận</h4>
                                <p class="text-gray-900">{{ $order->shipping_address['full_name'] ?? 'N/A' }}</p>
                                <p class="text-gray-600">{{ $order->shipping_address['phone'] ?? 'N/A' }}</p>
                                <p class="text-gray-600 mt-1">
                                    {{ $order->shipping_address['address_line1'] ?? '' }},
                                    {{ $order->shipping_address['city'] ?? '' }}
                                </p>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-700 uppercase mb-2">Ghi chú đơn hàng</h4>
                                <p class="text-gray-600 italic bg-gray-50 p-3 rounded border border-gray-200">
                                    "{{ $order->metadata['note'] ?? 'Không có ghi chú' }}"
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CỘT PHẢI: TRẠNG THÁI & THANH TOÁN --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- 3. Trạng thái Đơn hàng (Module F) --}}
                    <div
                        class="bg-white shadow sm:rounded-lg p-6 border-t-4 {{ $order->status === 'completed' ? 'border-green-500' : ($order->status === 'cancelled' ? 'border-red-500' : 'border-indigo-500') }}">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Trạng thái Đơn hàng</h3>
                        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <select name="status"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @foreach($statuses as $st)
                                        <option value="{{ $st }}" {{ $order->status == $st ? 'selected' : '' }}>{{ ucfirst($st) }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-xs text-gray-500">
                                    * Lưu ý: Chuyển sang "Completed" có thể sẽ tự động đánh dấu đã thanh toán và giao hàng
                                    thành công.
                                </p>
                            </div>
                            <button type="submit"
                                class="w-full bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 flex items-center justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none">
                                Cập nhật Trạng thái
                            </button>
                        </form>

                        <div class="mt-6 border-t pt-4 text-sm text-gray-600 space-y-2">
                            <div class="flex justify-between">
                                <span>Ngày tạo:</span>
                                <span class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Cập nhật cuối:</span>
                                <span class="font-medium">{{ $order->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Quản lý Thanh toán (Module G - Payment) --}}
                    @if($order->payment)
                        <div
                            class="bg-white shadow sm:rounded-lg p-6 border-l-4 {{ $order->payment->status == 'paid' ? 'border-green-500' : ($order->payment->status == 'failed' ? 'border-red-500' : 'border-yellow-500') }}">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                    </path>
                                </svg>
                                Thanh toán
                            </h3>

                            <div class="mb-4">
                                <p class="text-sm text-gray-600">Phương thức: <strong
                                        class="uppercase text-gray-900">{{ $order->payment->method }}</strong></p>
                                <div class="mt-2 flex items-center">
                                    <span class="text-sm text-gray-600 mr-2">Trạng thái:</span>
                                    <span
                                        class="px-2 py-1 rounded text-xs font-bold {{ $order->payment->status == 'paid' ? 'bg-green-100 text-green-800' : ($order->payment->status == 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($order->payment->status) }}
                                    </span>
                                </div>
                                @if($order->payment->paid_at)
                                    <p class="text-xs text-gray-500 mt-1">Đã trả:
                                        {{ $order->payment->paid_at->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>

                            <form action="{{ route('admin.orders.update_payment', $order->payment->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cập nhật Payment</label>
                                <div class="flex gap-2">
                                    <select name="status"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        <option value="pending" {{ $order->payment->status == 'pending' ? 'selected' : '' }}>Chưa
                                            thanh toán</option>
                                        <option value="paid" {{ $order->payment->status == 'paid' ? 'selected' : '' }}>Đã thanh
                                            toán</option>
                                        <option value="failed" {{ $order->payment->status == 'failed' ? 'selected' : '' }}>Thất
                                            bại</option>
                                        <option value="refunded" {{ $order->payment->status == 'refunded' ? 'selected' : '' }}>
                                            Hoàn tiền</option>
                                    </select>
                                    <button type="submit"
                                        class="bg-green-600 text-white px-3 py-2 rounded shadow-sm hover:bg-green-700 text-sm">
                                        Lưu
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                    {{-- Nút Back --}}
                    <a href="{{ route('admin.orders.index') }}"
                        class="block w-full text-center bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
