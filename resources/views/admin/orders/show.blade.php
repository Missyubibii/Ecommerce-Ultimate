@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->order_number)
@section('header')
    <div class="flex justify-between items-center">
        <span>Đơn hàng <span class="text-indigo-600 font-mono">#{{ $order->order_number }}</span></span>
    </div>
@endsection

@section('content')
    @php
        $orderStatusLabels = [
            'pending'    => 'Chờ xử lý',
            'paid'       => 'Đã thanh toán',
            'processing' => 'Đang chuẩn bị hàng',
            'shipped'    => 'Đang giao hàng',
            'completed'  => 'Hoàn thành',
            'cancelled'  => 'Đã hủy',
            'refunded'   => 'Đã hoàn tiền'
        ];

        $paymentStatusLabels = [
            'pending'  => 'Chờ thanh toán',
            'paid'     => 'Đã thanh toán',
            'failed'   => 'Thất bại',
            'refunded' => 'Đã hoàn tiền'
        ];
    @endphp

    <div class="py-8" x-data="{ showShipModal: false, showCancelModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- STEPPER --}}
            <div class="bg-white shadow-sm sm:rounded-2xl p-8 mb-8 border border-gray-100">
                <x-order.stepper :status="$order->status" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- CỘT TRÁI: DANH SÁCH SẢN PHẨM & THÔNG TIN --}}
                <div class="lg:col-span-2 space-y-8">
                    
                    {{-- ACTION CENTER (Nút bấm theo quy trình) --}}
                    <div class="bg-white shadow-sm sm:rounded-2xl overflow-hidden border-2 border-indigo-500 ring-4 ring-indigo-50">
                        <div class="px-6 py-4 bg-indigo-500 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-white">Trung tâm xử lý đơn hàng</h3>
                            <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-bold text-white uppercase tracking-widest">
                                {{ $orderStatusLabels[$order->status] ?? $order->status }}
                            </span>
                        </div>
                        <div class="p-8">
                            <div class="flex flex-col md:flex-row items-center gap-6">
                                <div class="flex-1">
                                    <h4 class="text-xl font-bold text-gray-900 mb-2">
                                        @if($order->status === 'pending') Bước tiếp theo: Xác nhận đơn hàng
                                        @elseif($order->status === 'processing') Bước tiếp theo: Giao hàng cho ĐVVC
                                        @elseif($order->status === 'shipped') Bước tiếp theo: Hoàn thành đơn hàng
                                        @elseif($order->status === 'completed') Đơn hàng đã kết thúc
                                        @else Đơn hàng không khả dụng
                                        @endif
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        @if($order->status === 'pending') Kiểm tra tồn kho và thông tin khách hàng trước khi bấm xác nhận.
                                        @elseif($order->status === 'processing') Chuẩn bị hàng hóa và đóng gói, sau đó nhập mã vận đơn để gửi đi.
                                        @elseif($order->status === 'shipped') Theo dõi quá trình vận chuyển và xác nhận khi khách đã nhận hàng.
                                        @elseif($order->status === 'completed') Cảm ơn bạn! Đơn hàng này đã được lưu trữ an toàn.
                                        @endif
                                    </p>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    @if($order->status === 'pending')
                                        <form action="{{ route('admin.orders.approve', $order->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all">
                                                <i data-lucide="check" class="w-5 h-5 mr-2"></i> Xác nhận đơn hàng
                                            </button>
                                        </form>
                                        <button @click="showCancelModal = true" class="inline-flex items-center px-6 py-3 bg-white border border-red-200 text-red-600 hover:bg-red-50 font-bold rounded-xl transition-all">
                                            Hủy đơn
                                        </button>
                                    @elseif($order->status === 'processing')
                                        <button @click="showShipModal = true" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-200 transition-all">
                                            <i data-lucide="truck" class="w-5 h-5 mr-2"></i> Tiến hành giao hàng
                                        </button>
                                        <button @click="showCancelModal = true" class="inline-flex items-center px-4 py-2 text-red-500 hover:underline text-sm font-bold">
                                            Hủy đơn
                                        </button>
                                    @elseif($order->status === 'shipped')
                                        <form action="{{ route('admin.orders.complete', $order->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-200 transition-all">
                                                <i data-lucide="flag" class="w-5 h-5 mr-2"></i> Hoàn thành đơn hàng
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($order->status === 'completed')
                                        <button class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-500 font-bold rounded-xl cursor-not-allowed">
                                            <i data-lucide="lock" class="w-5 h-5 mr-2"></i> Đã hoàn thành
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 1. Danh sách sản phẩm --}}
                    <div class="bg-white shadow-sm overflow-hidden sm:rounded-2xl border border-gray-100">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900">Sản phẩm đơn hàng</h3>
                            <span class="text-xs font-bold text-gray-400">#{{ $order->order_number }}</span>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <li class="p-6 flex items-center hover:bg-gray-50 transition-colors">
                                    <div class="flex-shrink-0 h-20 w-20 border border-gray-100 rounded-xl overflow-hidden bg-white shadow-sm">
                                        @php
                                            $imgSnapshot = $item->product_snapshot['image'] ?? $item->product_snapshot['image_url'] ?? null;
                                            if ($imgSnapshot && !str_starts_with($imgSnapshot, 'http')) {
                                                $imgSrc = asset('storage/' . $imgSnapshot);
                                            } else {
                                                $imgSrc = $imgSnapshot ?? 'https://placehold.co/100?text=No+Image';
                                            }
                                        @endphp
                                        <img class="h-full w-full object-contain" src="{{ $imgSrc }}" alt="{{ $item->product_name }}">
                                    </div>
                                    <div class="ml-6 flex-1">
                                        <div class="flex justify-between">
                                            <div>
                                                <h4 class="text-base font-bold text-gray-900">{{ $item->product_name }}</h4>
                                                <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest font-bold">SKU: {{ $item->product_snapshot['sku'] ?? 'N/A' }}</p>
                                            </div>
                                            <p class="text-base font-bold text-indigo-600">{{ number_format($item->subtotal, 0, ',', '.') }}đ</p>
                                        </div>
                                        <div class="flex justify-between mt-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                                                SL: {{ $item->quantity }}
                                            </span>
                                            <span class="text-sm text-gray-400">Đơn giá: {{ number_format($item->unit_price, 0, ',', '.') }}đ</span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="bg-gray-50/50 px-8 py-6 border-t border-gray-100">
                            <div class="flex flex-col gap-3 max-w-xs ml-auto">
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>Tạm tính:</span>
                                    <span class="font-bold text-gray-700">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>Phí vận chuyển:</span>
                                    <span class="font-bold text-gray-700">{{ number_format($order->shipping_amount ?? 0, 0, ',', '.') }}đ</span>
                                </div>
                                <div class="flex justify-between text-xl font-extrabold text-indigo-600 pt-3 border-t border-gray-200 mt-2">
                                    <span>TỔNG CỘNG:</span>
                                    <span>{{ number_format(($order->total_amount + ($order->shipping_amount ?? 0)), 0, ',', '.') }}đ</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Thông tin Khách hàng & Địa chỉ --}}
                    <div class="bg-white shadow-sm sm:rounded-2xl overflow-hidden border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-lg font-bold text-gray-900">Thông tin khách hàng</h3>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="flex items-start gap-4">
                                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl">
                                    <i data-lucide="user" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Người nhận hàng</h4>
                                    <p class="text-lg font-bold text-gray-900">{{ $order->shipping_address['full_name'] ?? 'N/A' }}</p>
                                    <p class="text-gray-500 mt-1 font-medium">{{ $order->shipping_address['phone'] ?? 'N/A' }}</p>
                                    <div class="mt-4 flex items-start gap-2 text-gray-600">
                                        <i data-lucide="map-pin" class="w-4 h-4 mt-1 shrink-0"></i>
                                        <p class="text-sm leading-relaxed">
                                            {{ $order->shipping_address['address_line1'] ?? '' }},
                                            {{ $order->shipping_address['city'] ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl">
                                    <i data-lucide="message-square" class="w-6 h-6"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Ghi chú từ khách</h4>
                                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 italic text-sm text-gray-600">
                                        "{{ $order->metadata['note'] ?? 'Không có ghi chú nào từ khách hàng.' }}"
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CỘT PHẢI: TRẠNG THÁI & THANH TOÁN --}}
                <div class="lg:col-span-1 space-y-8">
                    
                    {{-- THÔNG TIN THANH TOÁN --}}
                    @if($order->payment)
                        <div class="bg-white shadow-sm sm:rounded-2xl p-6 border border-gray-100">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-bold text-gray-900">Thanh toán</h3>
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest {{ $order->payment->status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $paymentStatusLabels[$order->payment->status] ?? $order->payment->status }}
                                </span>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Phương thức:</span>
                                    <span class="font-bold text-gray-900 uppercase">{{ $order->payment->method }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Số tiền:</span>
                                    <span class="font-bold text-indigo-600">{{ number_format($order->payment->amount, 0, ',', '.') }}đ</span>
                                </div>
                                @if($order->payment->paid_at)
                                    <div class="flex justify-between items-center text-sm pt-4 border-t border-gray-50">
                                        <span class="text-gray-500">Thời gian:</span>
                                        <span class="text-gray-900 font-medium">{{ $order->payment->paid_at->format('H:i d/m/Y') }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div x-data="{ open: false }" class="mt-6">
                                <button @click="open = !open" class="text-xs font-bold text-indigo-600 hover:underline">Sửa thủ công</button>
                                <div x-show="open" x-cloak class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                    <form action="{{ route('admin.orders.update_payment', $order->payment->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="block w-full text-xs border-gray-200 rounded-lg mb-2">
                                            @foreach($paymentStatusLabels as $key => $label)
                                                <option value="{{ $key }}" {{ $order->payment->status == $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="w-full bg-white border border-gray-200 py-1.5 rounded-lg text-xs font-bold hover:bg-gray-50">Lưu</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- THÔNG TIN VẬN CHUYỂN --}}
                    @if($order->shipment)
                        <div class="bg-white shadow-sm sm:rounded-2xl p-6 border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900 mb-6">Vận chuyển</h3>
                            
                            @if($order->shipment->carrier)
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                            <i data-lucide="truck" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $order->shipment->carrier }}</p>
                                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">{{ $order->shipment->tracking_number ?? 'Chưa có mã tracking' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="p-3 bg-gray-50 rounded-xl text-xs font-bold text-gray-600 flex justify-between items-center">
                                        <span>Trạng thái:</span>
                                        <span class="text-blue-600 uppercase">{{ $order->shipment->status }}</span>
                                    </div>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic text-center py-4">Chưa có thông tin vận chuyển.</p>
                            @endif
                        </div>
                    @endif

                    {{-- TIMELINE --}}
                    <div class="bg-white shadow-sm sm:rounded-2xl p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Nhật ký xử lý</h3>
                        <div class="flow-root">
                            <ul class="-mb-8">
                                @forelse($activities as $activity)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-100" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-gray-50 flex items-center justify-center ring-4 ring-white">
                                                        <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5">
                                                    <p class="text-xs font-bold text-gray-900 leading-tight">{{ $activity->description }}</p>
                                                    <p class="text-[10px] text-gray-400 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <p class="text-center text-xs text-gray-400 italic">Chưa có dữ liệu.</p>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL GIAO HÀNG --}}
        <div x-show="showShipModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div @click.away="showShipModal = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
                <div class="px-8 py-6 bg-indigo-600">
                    <h3 class="text-xl font-bold text-white">Xác nhận giao hàng</h3>
                    <p class="text-indigo-100 text-sm mt-1">Vui lòng cung cấp thông tin vận chuyển.</p>
                </div>
                <form action="{{ route('admin.orders.ship', $order->id) }}" method="POST" class="p-8">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Đơn vị vận chuyển</label>
                            <input type="text" name="carrier" required placeholder="Ví dụ: Giao Hàng Tiết Kiệm, VNPost..."
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Mã vận đơn (Tracking Number)</label>
                            <input type="text" name="tracking_number" placeholder="Nhập mã vận đơn nếu có"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div class="mt-8 flex gap-3">
                        <button type="button" @click="showShipModal = false" class="flex-1 px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition-all">Hủy</button>
                        <button type="submit" class="flex-2 px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">Bắt đầu giao hàng</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL HỦY ĐƠN --}}
        <div x-show="showCancelModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div @click.away="showCancelModal = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
                <div class="px-8 py-6 bg-red-600">
                    <h3 class="text-xl font-bold text-white">Xác nhận hủy đơn hàng</h3>
                </div>
                <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" class="p-8 text-center">
                    @csrf
                    <div class="mb-6">
                        <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                        </div>
                        <p class="text-gray-600">Bạn có chắc chắn muốn hủy đơn hàng này? Hành động này sẽ hoàn lại số lượng sản phẩm vào kho.</p>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="showCancelModal = false" class="flex-1 px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200">Bỏ qua</button>
                        <button type="submit" class="flex-1 px-6 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 shadow-lg shadow-red-100">Đồng ý hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
