@extends('layouts.admin')
@section('title', 'Sửa Banner')
@section('header', 'Cập nhật Banner')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow-lg">
        <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Tiêu đề</label>
                    <input type="text" name="title" value="{{ $banner->title }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Vị trí</label>
                    <select name="position"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <optgroup label="Slide Chính">
                            <option value="main_slider" {{ $banner->position == 'main_slider' ? 'selected' : '' }}>Slide Chính
                            </option>
                        </optgroup>
                        <optgroup label="Banner Phụ (Cột Phải)">
                            <option value="pos_1" {{ $banner->position == 'pos_1' ? 'selected' : '' }}>Vị trí 1 (Trên cùng -
                                Phải)</option>
                            <option value="pos_2" {{ $banner->position == 'pos_2' ? 'selected' : '' }}>Vị trí 2 (Dưới - Phải)
                            </option>
                        </optgroup>
                        <optgroup label="Banner Hàng Dưới">
                            <option value="pos_3" {{ $banner->position == 'pos_3' ? 'selected' : '' }}>Vị trí 3 (Trái)
                            </option>
                            <option value="pos_4" {{ $banner->position == 'pos_4' ? 'selected' : '' }}>Vị trí 4 (Giữa)
                            </option>
                            <option value="pos_5" {{ $banner->position == 'pos_5' ? 'selected' : '' }}>Vị trí 5 (Phải)
                            </option>
                            <option value="pos_6" {{ $banner->position == 'pos_6' ? 'selected' : '' }}>Vị trí 6 (Dự phòng)
                            </option>
                        </optgroup>
                        <optgroup label="Khác">
                            <option value="header_top" {{ $banner->position == 'header_top' ? 'selected' : '' }}>Header Top
                            </option>
                        </optgroup>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Thứ tự</label>
                    <input type="number" name="sort_order" value="{{ $banner->sort_order }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">URL</label>
                    <input type="url" name="url" value="{{ $banner->url }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Hình ảnh hiện tại</label>
                    @if($banner->image)
                        <img src="{{ $banner->image_url }}" class="mt-2 h-32 w-auto object-cover rounded border">
                    @endif
                    <label class="block text-sm font-medium text-gray-700 mt-4">Thay đổi ảnh</label>
                    <input type="file" name="image" accept="image/*"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2 flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600">Đang kích hoạt</span>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.banners.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Hủy</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Cập
                    nhật</button>
            </div>
        </form>
    </div>
@endsection
