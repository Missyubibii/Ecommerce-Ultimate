@extends('layouts.admin')

@section('title', 'Quản lý Thương hiệu')
@section('header', 'Danh sách Thương hiệu')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div class="text-sm text-gray-500">Quản lý các thương hiệu sản phẩm và vị trí quảng cáo.</div>
        <a href="{{ route('admin.brands.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i> Thêm Thương hiệu
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Thương hiệu</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Vị trí hiển thị</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Thứ tự</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($brands as $brand)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg border bg-gray-50 overflow-hidden flex-shrink-0">
                                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="w-full h-full object-contain p-1">
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900">{{ $brand->name }}</div>
                                <div class="text-xs text-gray-500">{{ $brand->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @php
                                $locations = [
                                    'home' => ['label' => 'Trang chủ', 'color' => 'bg-blue-100 text-blue-700'],
                                    'category' => ['label' => 'Danh mục', 'color' => 'bg-purple-100 text-purple-700'],
                                    'product_detail' => ['label' => 'Chi tiết', 'color' => 'bg-green-100 text-green-700'],
                                ];
                            @endphp
                            @if(!empty($brand->display_locations))
                                @foreach($brand->display_locations as $loc)
                                    @if(isset($locations[$loc]))
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $locations[$loc]['color'] }}">
                                            {{ $locations[$loc]['label'] }}
                                        </span>
                                    @endif
                                @endforeach
                            @else
                                <span class="text-xs text-gray-400 italic">Không có</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($brand->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full border border-green-200">Hoạt động</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-500 text-xs font-bold rounded-full border border-gray-200">Ẩn</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $brand->sort_order }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.brands.edit', $brand) }}" class="p-1 text-blue-600 hover:bg-blue-50 rounded transition" title="Sửa">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" onsubmit="return confirm('Xóa thương hiệu này?');" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1 text-red-600 hover:bg-red-50 rounded transition" title="Xóa">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">Chưa có thương hiệu nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $brands->links() }}
    </div>
</div>
@endsection
