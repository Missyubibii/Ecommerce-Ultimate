@extends('layouts.admin')

@section('title', 'Sửa Thương hiệu')
@section('header', 'Cập nhật: ' . $brand->name)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                {{-- Tên & Slug --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tên thương hiệu <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $brand->name) }}" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $brand->slug) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                </div>

                {{-- Logo --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Logo thương hiệu</label>
                    <div class="mt-1 flex items-center gap-4" x-data="{ preview: '{{ $brand->logo_url }}' }">
                        <div class="w-20 h-20 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 overflow-hidden">
                            <img :src="preview" class="w-full h-full object-contain p-1">
                        </div>
                        <label class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer transition shadow-sm">
                            Thay đổi ảnh
                            <input type="file" name="logo" class="hidden" accept="image/*" 
                                @change="const file = $el.files[0]; if (file) { preview = URL.createObjectURL(file); }">
                        </label>
                    </div>
                </div>

                {{-- Mô tả --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Mô tả hãng</label>
                    <textarea name="description" rows="3" 
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">{{ old('description', $brand->description) }}</textarea>
                </div>

                {{-- Vị trí quảng cáo --}}
                <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-100">
                    <label class="block text-sm font-bold text-indigo-900 mb-3">Cấu hình quảng cáo thương hiệu</label>
                    <div class="space-y-2">
                        @php $currentLocs = $brand->display_locations ?? []; @endphp
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="display_locations[]" value="home" {{ in_array('home', $currentLocs) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700 group-hover:text-indigo-600 transition">Hiển thị ở dải slide Trang chủ</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="display_locations[]" value="category" {{ in_array('category', $currentLocs) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700 group-hover:text-indigo-600 transition">Hiển thị ở trang Danh mục sản phẩm</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="display_locations[]" value="product_detail" {{ in_array('product_detail', $currentLocs) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700 group-hover:text-indigo-600 transition">Hiển thị ở trang Chi tiết sản phẩm</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Thứ tự ưu tiên</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $brand->sort_order) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </div>
                    <div class="flex items-end pb-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ $brand->is_active ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm font-bold text-gray-700">Đang hoạt động</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t">
                <a href="{{ route('admin.brands.index') }}" class="px-6 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 text-gray-700 transition font-medium">Hủy</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium shadow-md">Cập nhật Thương hiệu</button>
            </div>
        </form>
    </div>
</div>
@endsection
