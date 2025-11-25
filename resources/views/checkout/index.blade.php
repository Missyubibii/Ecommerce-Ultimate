<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Thanh toán</h1>

        <form action="{{ route('order.place') }}" method="POST" class="flex flex-col lg:flex-row gap-8">
            @csrf

            <div class="w-full lg:w-2/3 space-y-6">

                <div class="bg-white p-6 shadow rounded-lg">
                    <h2 class="text-lg font-bold mb-4 flex items-center">
                        <span class="bg-blue-100 text-blue-600 w-8 h-8 rounded-full flex items-center justify-center mr-2">1</span>
                        Địa chỉ nhận hàng
                    </h2>

                    @if(count($addresses) > 0)
                        <div class="space-y-3">
                            @foreach($addresses as $addr)
                                <label class="flex items-start p-3 border rounded cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="address_id" value="{{ $addr->id }}" {{ $addr->is_default ? 'checked' : '' }} class="mt-1 mr-3">
                                    <div>
                                        <p class="font-bold">{{ $addr->full_name }} <span class="text-gray-500 font-normal">({{ $addr->phone }})</span></p>
                                        <p class="text-sm text-gray-600">{{ $addr->address_line1 }}, {{ $addr->city }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <button type="button" class="text-blue-600 text-sm hover:underline">+ Thêm địa chỉ mới</button>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <x-input-label>Họ tên</x-input-label>
                                <x-text-input name="new_address[full_name]" class="w-full" value="{{ optional($user)->name }}" required />
                            </div>
                            <div class="col-span-1">
                                <x-input-label>Số điện thoại</x-input-label>
                                <x-text-input name="new_address[phone]" class="w-full" value="{{ optional($user)->phone }}" required />
                            </div>
                            <div class="col-span-2">
                                <x-input-label>Địa chỉ</x-input-label>
                                <x-text-input name="new_address[address_line1]" class="w-full" required />
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bg-white p-6 shadow rounded-lg">
                    <h2 class="text-lg font-bold mb-4 flex items-center">
                        <span class="bg-blue-100 text-blue-600 w-8 h-8 rounded-full flex items-center justify-center mr-2">2</span>
                        Thanh toán
                    </h2>
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border rounded cursor-pointer">
                            <input type="radio" name="payment_method" value="cod" checked class="mr-3">
                            <span>Thanh toán khi nhận hàng (COD)</span>
                        </label>
                        <label class="flex items-center p-3 border rounded cursor-pointer">
                            <input type="radio" name="payment_method" value="banking" class="mr-3">
                            <span>Chuyển khoản ngân hàng (QR Code)</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-1/3">
                <div class="bg-white p-6 shadow rounded-lg sticky top-4">
                    <h2 class="text-lg font-bold mb-4">Đơn hàng</h2>

                    <div class="max-h-60 overflow-y-auto mb-4 divide-y">
                        @foreach($cart['items'] as $item)
                            <div class="py-2 flex justify-between text-sm">
                                <div class="flex-1">
                                    <span class="font-medium">{{ $item->product->name }}</span>
                                    <div class="text-gray-500">x {{ $item->quantity }}</div>
                                </div>
                                <div>{{ number_format($item->total, 0, ',', '.') }}đ</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tạm tính</span>
                            <span>{{ number_format($cart['subtotal'], 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Vận chuyển</span>
                            <span>Miễn phí</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold text-red-600 pt-2 border-t mt-2">
                            <span>Tổng tiền</span>
                            <span>{{ number_format($cart['subtotal'], 0, ',', '.') }}đ</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-6 bg-red-600 text-white py-3 rounded font-bold hover:bg-red-700 transition">
                        ĐẶT HÀNG
                    </button>

                    <p class="text-xs text-gray-500 mt-2 text-center">
                        Nhấn "Đặt hàng" đồng nghĩa với việc bạn đồng ý với điều khoản của chúng tôi.
                    </p>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
