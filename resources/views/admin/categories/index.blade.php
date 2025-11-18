@extends('layouts.admin')
@section('title', 'Quản lý Danh mục')
@section('header', 'Danh mục sản phẩm')

@section('content')
    <div class="bg-white rounded-lg shadow overflow-hidden p-6">
        <div class="flex justify-between mb-4">
            <h3 class="text-lg font-medium">Cấu trúc danh mục</h3>
            <a href="{{ route('admin.categories.create') }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">Thêm danh mục</a>
        </div>

        <div class="border border-gray-200 rounded-md">
            <ul class="divide-y divide-gray-200">
                @foreach($categories as $root)
                    <li class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-center">
                            <div class="font-bold text-gray-800">{{ $root->name }}</div>
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.categories.edit', $root) }}" class="text-indigo-600 text-sm">Sửa</a>
                                <form action="{{ route('admin.categories.destroy', $root) }}" method="POST"
                                    onsubmit="return confirm('Xóa danh mục này?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 text-sm">Xóa</button>
                                </form>
                            </div>
                        </div>
                        {{-- Render Children --}}
                        @if($root->children->isNotEmpty())
                            <ul class="ml-6 mt-2 border-l-2 border-gray-300 pl-4 space-y-2">
                                @foreach($root->children as $child)
                                    <li class="flex justify-between items-center text-sm">
                                        <span>{{ $child->name }}</span>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.categories.edit', $child) }}" class="text-indigo-600">Sửa</a>
                                            <form action="{{ route('admin.categories.destroy', $child) }}" method="POST"
                                                onsubmit="return confirm('Xóa danh mục này?')" class="inline">
                                                @csrf @method('DELETE')
                                                <button class="text-red-600">Xóa</button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
