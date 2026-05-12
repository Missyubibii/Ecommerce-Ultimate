@props(['recentOrders'])

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-50 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-900">Đơn hàng gần đây</h3>
        <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">Xem tất cả</a>
    </div>
    <div class="overflow-x-auto overflow-y-auto max-h-[400px]">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-[10px] uppercase font-bold text-gray-500 sticky top-0 z-10 shadow-sm">
                <tr>
                    <th class="px-6 py-3">Mã đơn</th>
                    <th class="px-6 py-3">Khách hàng</th>
                    <th class="px-6 py-3">Tổng tiền</th>
                    <th class="px-6 py-3">Trạng thái</th>
                    <th class="px-6 py-3">Thanh toán</th>
                    <th class="px-6 py-3">Ngày đặt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($recentOrders as $order)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-sm font-bold text-indigo-600 hover:underline">
                                #{{ $order->order_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-900">{{ $order->user->name ?? 'Khách vãng lai' }}</p>
                            <p class="text-[10px] text-gray-500">{{ $order->user->email ?? '' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">
                            {{ number_format($order->total_amount, 0, ',', '.') }}đ
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-amber-50 text-amber-600',
                                    'processing' => 'bg-blue-50 text-blue-600',
                                    'completed' => 'bg-emerald-50 text-emerald-600',
                                    'cancelled' => 'bg-red-50 text-red-600',
                                ];
                                $statusLabels = [
                                    'pending' => 'Chờ xác nhận',
                                    'processing' => 'Đang xử lý',
                                    'completed' => 'Hoàn thành',
                                    'cancelled' => 'Đã hủy',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $statusClasses[$order->status] ?? 'bg-gray-50 text-gray-600' }}">
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($order->payment && $order->payment->paid_at)
                                <span class="flex items-center gap-1 text-[10px] font-bold text-emerald-600">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                                    Đã thanh toán
                                </span>
                            @else
                                <span class="flex items-center gap-1 text-[10px] font-bold text-gray-400">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"></path></svg>
                                    Chưa thanh toán
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
