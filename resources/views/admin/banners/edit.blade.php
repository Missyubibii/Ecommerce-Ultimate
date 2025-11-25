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
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Vị trí</label>
                    <select name="position" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="main_slider" {{ $banner->position == 'main_slider' ? 'selected' : '' }}>Slide Chính
                        </option>
                        <option value="header_top" {{ $banner->position == 'header_top' ? 'selected' : '' }}>Header Top
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Thứ tự</label>
                    <input type="number" name="sort_order" value="{{ $banner->sort_order }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">URL</label>
                    <input type="url" name="url" value="{{ $banner->url }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Hình ảnh hiện tại</label>
                    @if($banner->image)
                        <img src="{{ $banner->image_url }}" class="mt-2 h-32 w-auto object-cover rounded border">
                    @endif
                    <label class="block text-sm font-medium text-gray-700 mt-4">Thay đổi ảnh</label>
                    <input type="file" name="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500">
                </div>

                <div class="col-span-2 flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 shadow-sm">
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
