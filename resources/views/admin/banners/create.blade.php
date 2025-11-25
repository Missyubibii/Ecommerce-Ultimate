@extends('layouts.admin')
@section('title', 'Thêm Banner')
@section('header', 'Thêm Banner Mới')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow-lg">
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Title --}}
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Tiêu đề (Optional)</label>
                    <input type="text" name="title"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                {{-- Position --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Vị trí hiển thị</label>
                    <select name="position" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="main_slider">Slide Chính (Trang chủ)</option>
                        <option value="header_top">Header Top (Chạy chữ/Ảnh nhỏ)</option>
                    </select>
                </div>

                {{-- Sort Order --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Thứ tự hiển thị</label>
                    <input type="number" name="sort_order" value="0"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                {{-- URL --}}
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Link liên kết (URL)</label>
                    <input type="url" name="url" placeholder="https://..."
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                {{-- Image --}}
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Hình ảnh</label>
                    <input type="file" name="image" accept="image/*"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-1 text-xs text-gray-500">Slide chính nên dùng ảnh ngang (1200x400px). Header top nên dùng
                        ảnh nhỏ hoặc icon.</p>
                </div>

                {{-- Active --}}
                <div class="col-span-2 flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600">Kích hoạt ngay</span>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.banners.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Hủy</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Lưu
                    Banner</button>
            </div>
        </form>
    </div>
@endsection
