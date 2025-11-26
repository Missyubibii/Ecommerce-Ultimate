@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Thanh toán</h1>

        {{-- Hiển thị lỗi nếu có --}}
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <ul class="list-disc pl-5 text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('order.place') }}" method="POST" class="flex flex-col lg:flex-row gap-8" id="checkoutForm">
            @csrf

            <div class="w-full lg:w-2/3 space-y-6">

                {{-- GUEST EMAIL & LOGIN PROMPT --}}
                @guest
                    <div class="bg-white p-6 shadow rounded-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-bold">Thông tin liên hệ</h2>
                            <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:underline">Đã có tài khoản? Đăng nhập</a>
                        </div>
                        <div>
                            <x-input-label for="email" value="Email nhận đơn hàng *" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required placeholder="example@gmail.com" />
                            <p class="text-xs text-gray-500 mt-1">Chúng tôi sẽ gửi xác nhận đơn hàng vào email này.</p>
                        </div>
                    </div>
                @endguest

                <div class="bg-white p-6 shadow rounded-lg" x-data="{ addressOption: '{{ count($addresses) > 0 ? 'existing' : 'new' }}' }">
                    <h2 class="text-lg font-bold mb-4 flex items-center">
                        <span class="bg-blue-100 text-blue-600 w-8 h-8 rounded-full flex items-center justify-center mr-2">1</span>
                        Địa chỉ nhận hàng
                    </h2>

                    @auth
                        @if(count($addresses) > 0)
                            <div class="mb-4 space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="address_selection" value="existing" x-model="addressOption" class="mr-2 text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-medium">Chọn từ sổ địa chỉ</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="address_selection" value="new" x-model="addressOption" class="mr-2 text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-medium">Nhập địa chỉ mới</span>
                                </label>
                            </div>

                            {{-- List Address --}}
                            <div x-show="addressOption === 'existing'" class="space-y-3 mb-4 pl-6">
                                @foreach($addresses as $addr)
                                    <label class="flex items-start p-3 border rounded cursor-pointer hover:bg-gray-50 {{ $loop->first ? 'border-indigo-200 bg-indigo-50' : '' }}">
                                        <input type="radio" name="address_id" value="{{ $addr->id }}" {{ $addr->is_default || $loop->first ? 'checked' : '' }} class="mt-1 mr-3 text-indigo-600 focus:ring-indigo-500">
                                        <div>
                                            <p class="font-bold text-gray-800">{{ $addr->full_name }} <span class="text-gray-500 font-normal">| {{ $addr->phone }}</span></p>
                                            <p class="text-sm text-gray-600">{{ $addr->address_line1 }}, {{ $addr->city }}, {{ $addr->state }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    @endauth

                    {{-- New Address Form --}}
                    <div x-show="addressOption === 'new'" class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-4">
                        <div class="col-span-2 md:col-span-1">
                            <x-input-label>Họ tên người nhận *</x-input-label>
                            <x-text-input name="new_address[full_name]" class="w-full mt-1" value="{{ optional($user)->name }}" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-input-label>Số điện thoại *</x-input-label>
                            <x-text-input name="new_address[phone]" class="w-full mt-1" value="{{ optional($user)->phone }}" />
                        </div>
                        <div class="col-span-2">
                            <x-input-label>Địa chỉ (Số nhà, tên đường) *</x-input-label>
                            <x-text-input name="new_address[address_line1]" class="w-full mt-1" placeholder="Ví dụ: 123 Nguyễn Trãi" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-input-label>Tỉnh / Thành phố *</x-input-label>
                            <x-text-input name="new_address[city]" class="w-full mt-1" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-input-label>Quận / Huyện</x-input-label>
                            <x-text-input name="new_address[state]" class="w-full mt-1" /> {{-- Tạm dùng field state --}}
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 shadow rounded-lg">
                    <h2 class="text-lg font-bold mb-4 flex items-center">
                        <span class="bg-blue-100 text-blue-600 w-8 h-8 rounded-full flex items-center justify-center mr-2">2</span>
                        Thanh toán & Ghi chú
                    </h2>
                    <div class="space-y-3 mb-4">
                        <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="cod" checked class="mr-3 text-indigo-600 focus:ring-indigo-500">
                            <div class="flex items-center gap-2">
                                <i data-lucide="truck" class="text-gray-500"></i>
                                <span>Thanh toán khi nhận hàng (COD)</span>
                            </div>
                        </label>
                        <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="banking" class="mr-3 text-indigo-600 focus:ring-indigo-500">
                            <div class="flex items-center gap-2">
                                <i data-lucide="credit-card" class="text-gray-500"></i>
                                <span>Chuyển khoản ngân hàng (QR Code)</span>
                            </div>
                        </label>
                    </div>

                    <div>
                        <x-input-label>Ghi chú đơn hàng (Tùy chọn)</x-input-label>
                        <textarea name="note" rows="2" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-1/3">
                <div class="bg-white p-6 shadow rounded-lg sticky top-24">
                    <h2 class="text-lg font-bold mb-4">Đơn hàng</h2>

                    <div class="max-h-60 overflow-y-auto mb-4 divide-y scrollbar-thin">
                        @foreach($cart['items'] as $item)
                            <div class="py-3 flex justify-between text-sm">
                                <div class="flex gap-3">
                                    <div class="relative">
                                        <img src="{{ $item->product->image_url }}" class="w-10 h-10 rounded object-cover border">
                                        <span class="absolute -top-1 -right-1 bg-gray-500 text-white text-[9px] w-4 h-4 flex items-center justify-center rounded-full">{{ $item->quantity }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-800 line-clamp-1">{{ $item->product->name }}</span>
                                        <div class="text-xs text-gray-500">{{ number_format($item->product->price, 0, ',', '.') }}đ</div>
                                    </div>
                                </div>
                                <div class="font-semibold">{{ number_format($item->total, 0, ',', '.') }}đ</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between text-gray-600 text-sm">
                            <span>Tạm tính</span>
                            <span>{{ number_format($cart['subtotal'], 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex justify-between text-gray-600 text-sm">
                            <span>Vận chuyển</span>
                            <span class="text-green-600">Miễn phí</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold text-indigo-600 pt-3 border-t mt-2">
                            <span>Tổng tiền</span>
                            <span>{{ number_format($cart['subtotal'], 0, ',', '.') }}đ</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-6 bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                        ĐẶT HÀNG NGAY
                    </button>

                    <p class="text-xs text-gray-500 mt-3 text-center">
                        Bằng việc đặt hàng, bạn đồng ý với điều khoản dịch vụ của chúng tôi.
                    </p>
                </div>
            </div>
        </form>
    </div>
@endsection
