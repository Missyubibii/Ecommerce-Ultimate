@extends('layouts.admin')

@section('title', 'Thêm Thương hiệu')
@section('header', 'Tạo Thương hiệu mới')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6">
                {{-- Tên & Slug --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tên thương hiệu <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition"
                            placeholder="VD: Apple, Samsung...">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Slug (Tùy chọn)</label>
                        <input type="text" name="slug" value="{{ old('slug') }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition"
                            placeholder="apple-store">
                    </div>
                </div>

                {{-- Logo --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Logo thương hiệu</label>
                    <div class="mt-1 flex items-center gap-4" x-data="{ preview: null }">
                        <div class="w-20 h-20 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 overflow-hidden">
                            <template x-if="preview">
                                <img :src="preview" class="w-full h-full object-contain p-1">
                            </template>
                            <template x-if="!preview">
                                <i data-lucide="image" class="w-8 h-8 text-gray-300"></i>
                            </template>
                        </div>
                        <label class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer transition shadow-sm">
                            Chọn ảnh
                            <input type="file" name="logo" class="hidden" accept="image/*" 
                                @change="const file = $el.files[0]; if (file) { preview = URL.createObjectURL(file); }">
                        </label>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Định dạng: JPG, PNG, WEBP. Tối đa 2MB.</p>
                </div>

                {{-- Mô tả --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Mô tả hãng</label>
                    <textarea name="description" rows="3" 
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">{{ old('description') }}</textarea>
                </div>

                {{-- Vị trí quảng cáo --}}
                <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-100">
                    <label class="block text-sm font-bold text-indigo-900 mb-3">Cấu hình quảng cáo thương hiệu</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="display_locations[]" value="home" class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700 group-hover:text-indigo-600 transition">Hiển thị ở dải slide Trang chủ</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="display_locations[]" value="category" class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700 group-hover:text-indigo-600 transition">Hiển thị ở trang Danh mục sản phẩm</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="display_locations[]" value="product_detail" class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700 group-hover:text-indigo-600 transition">Hiển thị ở trang Chi tiết sản phẩm</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Thứ tự ưu tiên</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                    <div class="flex items-end pb-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm font-bold text-gray-700">Đang hoạt động</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t">
                <a href="{{ route('admin.brands.index') }}" class="px-6 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 text-gray-700 transition font-medium">Hủy</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium shadow-md">Lưu Thương hiệu</button>
            </div>
        </form>
    </div>
</div>
@endsection
