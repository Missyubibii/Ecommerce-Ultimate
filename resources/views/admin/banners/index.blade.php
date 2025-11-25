@extends('layouts.admin')
@section('title', 'Quản lý Banner')
@section('header', 'Danh sách Banner')

@section('content')
    <div class="p-6 bg-white rounded-xl shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Quản lý Banner</h1>
            <a href="{{ route('admin.banners.create') }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                + Thêm Banner
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hình ảnh</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tiêu đề / Link</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vị trí</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Thứ tự</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($banners as $banner)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img src="{{ $banner->image_url }}" class="h-12 w-24 object-cover rounded border">
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $banner->title ?? 'No Title' }}</div>
                                <div class="text-xs text-blue-500 truncate w-48">{{ $banner->url }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 text-xs rounded-full {{ $banner->position == 'main_slider' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $banner->position == 'main_slider' ? 'Slide Chính' : 'Header Top' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm">{{ $banner->sort_order }}</td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-2 py-1 text-xs rounded-full {{ $banner->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $banner->is_active ? 'Hiện' : 'Ẩn' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.banners.edit', $banner->id) }}"
                                    class="text-indigo-600 hover:underline">Sửa</a>
                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Xóa banner này?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $banners->links() }}</div>
    </div>
@endsection
