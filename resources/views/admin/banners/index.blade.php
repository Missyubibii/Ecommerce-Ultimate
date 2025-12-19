@extends('layouts.admin')
@section('title', 'Quản lý Banner')
@section('header', 'Danh sách Banner')

@section('content')
    <div class="p-6 bg-white rounded-xl shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Quản lý Banner</h1>
            <a href="{{ route('admin.banners.create') }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                + Thêm Banner
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hình ảnh
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu đề /
                            Link</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vị trí
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thứ tự
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng
                            thái</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành
                            động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($banners as $banner)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{-- Using direct asset helper for storage link, fallback to image_url accessor logic if needed
                                --}}
                                <img src="{{ $banner->image_url }}"
                                    class="h-12 w-24 object-cover rounded border border-gray-200"
                                    onerror="this.onerror=null; this.src='https://placehold.co/100x50?text=No+Image';">
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $banner->title ?? 'No Title' }}</div>
                                <div class="text-xs text-blue-500 truncate w-48" title="{{ $banner->url }}">{{ $banner->url }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $posClass = match ($banner->position) {
                                        'main_slider' => 'bg-blue-100 text-blue-800',
                                        'header_top' => 'bg-gray-100 text-gray-800',
                                        default => 'bg-purple-100 text-purple-800'
                                    };

                                    $posName = match ($banner->position) {
                                        'main_slider' => 'Slide Chính',
                                        'header_top' => 'Header Top',
                                        'pos_1' => 'Vị trí 1 (Trên Phải)',
                                        'pos_2' => 'Vị trí 2 (Dưới Phải)',
                                        'pos_3' => 'Vị trí 3 (Dưới Trái)',
                                        'pos_4' => 'Vị trí 4 (Dưới Giữa)',
                                        'pos_5' => 'Vị trí 5 (Dưới Phải)',
                                        'pos_6' => 'Vị trí 6 (Dự phòng)',
                                        default => $banner->position
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full font-semibold {{ $posClass }}">
                                    {{ $posName }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $banner->sort_order }}</td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-2 py-1 text-xs rounded-full font-semibold {{ $banner->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $banner->is_active ? 'Hiện' : 'Ẩn' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.banners.edit', $banner->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 font-medium">Sửa</a>
                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Xóa banner này?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900 font-medium">Xóa</button>
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