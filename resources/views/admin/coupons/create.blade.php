@extends('layouts.admin')

@section('title', 'Tạo Mã giảm giá mới')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('admin.coupons.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Code -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Mã Coupon</label>
                                <input type="text" name="code" value="{{ old('code') }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm uppercase placeholder-gray-400"
                                    placeholder="VD: SALE2025">
                                @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                                <select name="is_active" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="1">Kích hoạt</option>
                                    <option value="0">Tạm ẩn</option>
                                </select>
                            </div>

                            <!-- Type -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Loại giảm giá</label>
                                <select name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="fixed">Tiền mặt (VNĐ)</option>
                                    <option value="percent">Phần trăm (%)</option>
                                </select>
                            </div>

                            <!-- Value -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Giá trị giảm</label>
                                <input type="number" name="value" value="{{ old('value') }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <p class="text-xs text-gray-500 mt-1">Nhập số tiền hoặc số % (VD: 50000 hoặc 10)</p>
                            </div>

                            <!-- Min Order -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Đơn hàng tối thiểu</label>
                                <input type="number" name="min_order_amount" value="{{ old('min_order_amount', 0) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- Usage Limit -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Giới hạn số lần dùng</label>
                                <input type="number" name="usage_limit" value="{{ old('usage_limit') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    placeholder="Để trống nếu không giới hạn">
                            </div>

                            <!-- Date Range -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Ngày bắt đầu</label>
                                <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Ngày kết thúc</label>
                                <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- Description -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                                <textarea name="description" rows="2"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3">
                            <a href="{{ route('admin.coupons.index') }}" class="text-gray-600 hover:underline">Hủy</a>
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                                Tạo Coupon
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
