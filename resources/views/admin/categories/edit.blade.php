@extends('layouts.admin')
@section('title', 'Chỉnh sửa Danh mục')
@section('header', 'Cập nhật danh mục: ' . $category->name)

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
        {{-- Form gửi đến route update, kèm ID của danh mục --}}
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Bắt buộc phải có để Laravel hiểu đây là yêu cầu cập nhật --}}

            <div class="space-y-4">
                {{-- 1. Tên danh mục --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tên danh mục</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                </div>

                {{-- 2. Slug --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Slug (Tùy chọn)</label>
                    <input type="text" name="slug" value="{{ old('slug', $category->slug) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                {{-- 3. Danh mục cha --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Danh mục cha</label>
                    <select name="parent_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Là danh mục gốc --</option>
                        @foreach($parents as $p)
                            {{-- Ẩn chính nó khỏi danh sách cha để tránh lỗi lồng nhau --}}
                            @if($p->id !== $category->id)
                                <option value="{{ $p->id }}" {{ $category->parent_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                {{-- 4. Mô tả --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                    <textarea name="description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $category->description) }}</textarea>
                </div>
            </div>

            {{-- KHỐI HÀNH ĐỘNG --}}
            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.categories.index') }}"
                    class="mr-3 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Hủy quay lại</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Lưu thay
                    đổi</button>
            </div>
        </form>
    </div>
@endsection