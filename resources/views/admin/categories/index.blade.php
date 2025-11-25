@extends('layouts.admin')

@section('title', 'Quản lý Danh mục')
@section('header', 'Danh mục sản phẩm')

@php
    $initialData = [
        'search' => request('q', ''),
    ];
@endphp

@section('content')
    <div x-data="categoryIndexPage(@js($initialData))" class="p-6 bg-white rounded-xl shadow-lg">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quản lý Danh mục</h1>
                <div class="mt-2 flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-indigo-500 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-600">Tổng gốc: {{ $categories->count() }}</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-600">Tổng con:
                            {{ $categories->pluck('children')->flatten()->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.categories.create') }}"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center transition duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Thêm danh mục
                </a>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex flex-wrap items-center gap-4">
                {{-- Search --}}
                <div class="flex-1 min-w-[200px]">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        {{-- Lưu ý: Search tree hơi phức tạp, ở đây demo search tên root category --}}
                        <input type="text" x-model="search" @keyup.enter="applyFilters()" placeholder="Tìm danh mục gốc..."
                            class="pl-10 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                {{-- Action Buttons --}}
                <button @click="applyFilters()"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300">
                    Lọc
                </button>
                <a href="{{ route('admin.categories.index') }}"
                    class="text-gray-500 hover:text-gray-700 text-sm underline">Xóa lọc</a>
            </div>
        </div>

        {{-- Flash Message --}}
        @if(session('success'))
            <div
                class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Categories List (Styled as Table-like rows) --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Tên danh
                                mục</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Slug</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-600 uppercase tracking-wider">Sản phẩm
                            </th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase tracking-wider">Hành động
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($categories as $root)
                            {{-- Parent Row (Group Container) --}}
                            <tr class="group hover:bg-indigo-50/50 transition-colors duration-200 relative">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-lg font-bold text-lg mr-3 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                                            {{ substr($root->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                                {{ $root->name }}
                                                @if($root->children->isNotEmpty())
                                                    <span
                                                        class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 border border-gray-200 group-hover:border-indigo-200 group-hover:text-indigo-700 transition-colors">
                                                        {{ $root->children->count() }} cấp dưới
                                                    </span>
                                                @endif
                                            </div>
                                            @if($root->children->isNotEmpty())
                                                <div
                                                    class="text-xs text-gray-400 mt-0.5 group-hover:text-indigo-500 transition-colors flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                    Di chuột xem danh mục con
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Child Categories Popup (Absolute) --}}
                                    @if($root->children->isNotEmpty())
                                        <div
                                            class="absolute left-0 right-0 top-full z-20 hidden group-hover:block shadow-2xl border-t border-indigo-100 animate-fade-in-down">
                                            <div class="bg-white rounded-b-lg border-x border-b border-gray-200 overflow-hidden">
                                                <table class="min-w-full divide-y divide-gray-100">
                                                    <tbody class="bg-indigo-50/30">
                                                        @foreach($root->children as $child)
                                                            <tr class="hover:bg-white transition-colors">
                                                                <td class="px-6 py-3 pl-16 whitespace-nowrap flex items-center w-1/3">
                                                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                                    </svg>
                                                                    <span
                                                                        class="text-sm font-medium text-gray-700">{{ $child->name }}</span>
                                                                </td>
                                                                <td class="px-6 py-3 text-gray-500 font-mono text-xs w-1/3">
                                                                    {{ $child->slug }}</td>
                                                                <td class="px-6 py-3 text-center text-sm text-gray-500">
                                                                    {{ $child->products_count ?? 0 }}
                                                                </td>
                                                                <td class="px-6 py-3 text-right text-sm font-medium space-x-2">
                                                                    <a href="{{ route('admin.categories.edit', $child) }}"
                                                                        class="text-indigo-600 hover:text-indigo-900 text-xs uppercase font-bold">Sửa</a>
                                                                    <span class="text-gray-300">|</span>
                                                                    <form action="{{ route('admin.categories.destroy', $child) }}"
                                                                        method="POST" onsubmit="return confirm('Xóa danh mục con này?')"
                                                                        class="inline">
                                                                        @csrf @method('DELETE')
                                                                        <button
                                                                            class="text-red-600 hover:text-red-900 text-xs uppercase font-bold">Xóa</button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono bg-white group-hover:bg-transparent">
                                    {{ $root->slug }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center bg-white group-hover:bg-transparent">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $root->products_count ?? 0 }} SP
                                    </span>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium bg-white group-hover:bg-transparent">
                                    <div
                                        class="flex items-center justify-end space-x-3 opacity-60 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.categories.edit', $root) }}"
                                            class="text-indigo-600 hover:text-indigo-900 font-semibold">Sửa</a>
                                        <form action="{{ route('admin.categories.destroy', $root) }}" method="POST"
                                            onsubmit="return confirm('Xóa danh mục này và tất cả danh mục con?')"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:text-red-900 font-semibold">Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-900">Chưa có danh mục nào</p>
                                        <p class="text-sm text-gray-500 mt-1">Hãy bắt đầu bằng cách tạo danh mục mới.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- CSS Animation --}}
    <style>
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.2s ease-out forwards;
        }

        /* Đảm bảo dropdown hiển thị trên cùng */
        tr.group:hover {
            z-index: 20;
        }
    </style>

    {{-- Alpine Script --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('categoryIndexPage', (init) => ({
                search: init.search,
                applyFilters() {
                    let params = new URLSearchParams(window.location.search);
                    if (this.search) params.set('q', this.search); else params.delete('q');
                    window.location.search = params.toString();
                }
            }));
        });
    </script>
@endsection
