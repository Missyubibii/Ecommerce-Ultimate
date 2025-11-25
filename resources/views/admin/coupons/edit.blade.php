@extends('layouts.admin')

@section('title', 'Cập nhật Mã giảm giá')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Code -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Mã Coupon</label>
                                <input type="text" name="code" value="{{ old('code', $coupon->code) }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm uppercase">
                            </div>

                            <!-- Status -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                                <select name="is_active" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="1" {{ $coupon->is_active ? 'selected' : '' }}>Kích hoạt</option>
                                    <option value="0" {{ !$coupon->is_active ? 'selected' : '' }}>Tạm ẩn</option>
                                </select>
                            </div>

                            <!-- Type -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Loại giảm giá</label>
                                <select name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="fixed" {{ $coupon->type == 'fixed' ? 'selected' : '' }}>Tiền mặt (VNĐ)
                                    </option>
                                    <option value="percent" {{ $coupon->type == 'percent' ? 'selected' : '' }}>Phần trăm (%)
                                    </option>
                                </select>
                            </div>

                            <!-- Value -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Giá trị giảm</label>
                                <input type="number" name="value" value="{{ old('value', $coupon->value) }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- Min Order -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Đơn hàng tối thiểu</label>
                                <input type="number" name="min_order_amount"
                                    value="{{ old('min_order_amount', $coupon->min_order_amount) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- Usage Limit -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Giới hạn số lần dùng</label>
                                <input type="number" name="usage_limit"
                                    value="{{ old('usage_limit', $coupon->usage_limit) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- Date Range -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Ngày bắt đầu</label>
                                <input type="datetime-local" name="starts_at"
                                    value="{{ $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '' }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Ngày kết thúc</label>
                                <input type="datetime-local" name="ends_at"
                                    value="{{ $coupon->ends_at ? $coupon->ends_at->format('Y-m-d\TH:i') : '' }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- Description -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                                <textarea name="description" rows="2"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $coupon->description }}</textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3">
                            <a href="{{ route('admin.coupons.index') }}" class="text-gray-600 hover:underline">Hủy</a>
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                                Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
