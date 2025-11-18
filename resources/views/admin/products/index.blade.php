@extends('layouts.admin')

@section('title', 'Quản lý Sản phẩm')
@section('header', 'Danh sách sản phẩm')

@php
    // Chuẩn bị dữ liệu cho Alpine.js
    $initialData = [
        'search' => request('q', ''),
        'categoryFilter' => request('category_id', ''),
        'statusFilter' => request('status', ''),
        'allProductIds' => $allProductIds,
    ];
@endphp

@section('content')
<div x-data="productIndexPage(@js($initialData))" class="p-6 bg-white rounded-xl shadow-lg">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quản lý Sản phẩm</h1>
            <div class="mt-2 flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-600">Tổng: {{ $products->total() }} sản phẩm</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-600">Tồn kho thấp: {{ $lowStockCount }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.products.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center transition duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Thêm sản phẩm
            </a>
        </div>
    </div>

    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Search Input --}}
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" x-model="search" @keyup.enter="applyFilters()"
                        placeholder="Tìm theo tên, SKU..."
                        class="pl-10 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            {{-- Filter: Category --}}
            <div class="min-w-[180px]">
                <select x-model="categoryFilter" @change="applyFilters()" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Tất cả danh mục --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter: Status --}}
            <div class="min-w-[150px]">
                <select x-model="statusFilter" @change="applyFilters()" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Trạng thái --</option>
                    <option value="active">Đang bán</option>
                    <option value="draft">Bản nháp</option>
                </select>
            </div>

            {{-- Filter Button --}}
            <button @click="applyFilters()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300">
                Lọc
            </button>
             {{-- Reset Button --}}
             <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-gray-700 text-sm underline">Xóa lọc</a>
        </div>
    </div>

    <div x-show="selectedProducts.length > 0" x-transition
         class="mb-4 p-3 bg-indigo-50 border border-indigo-100 rounded-lg flex items-center justify-between">
        <div class="flex items-center">
            <span class="text-sm font-semibold text-indigo-800">Đã chọn <span x-text="selectedProducts.length"></span> sản phẩm</span>
        </div>
        <div class="flex items-center space-x-2">
            <button @click="bulkDelete()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm transition duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                Xóa đã chọn
            </button>
            <button @click="selectedProducts = []; selectAll = false;" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold py-1 px-3 rounded text-sm transition duration-300">
                Hủy
            </button>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left w-10">
                            <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Sản phẩm</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Danh mục</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Giá bán</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Tồn kho</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Trạng thái</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-4 py-3">
                                <input type="checkbox" value="{{ $product->id }}" x-model="selectedProducts" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 mr-3">
                                        @if($product->image)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-10 w-10 rounded object-cover mr-3 border border-gray-200">
                                        @else
                                            <div class="h-10 w-10 rounded bg-gray-100 flex items-center justify-center text-gray-400 text-xs">N/A</div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 group-hover:text-indigo-600">{{ $product->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $product->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $product->category?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-800">
                                {{ number_format($product->price, 0, ',', '.') }} đ
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->quantity <= $product->min_stock ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $product->quantity }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($product->status === 'active')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Draft</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('admin.products.show', $product) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs uppercase tracking-wider">Xem</a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-900 font-medium text-xs uppercase tracking-wider">Sửa</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                <p class="mt-2 text-sm font-medium">Không tìm thấy sản phẩm nào.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $products->withQueryString()->links() }}
    </div>

</div>

{{-- 6. JAVASCRIPT LOGIC FOR ALPINE --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('productIndexPage', (initialData) => ({
            search: initialData.search,
            categoryFilter: initialData.categoryFilter,
            statusFilter: initialData.statusFilter,
            allProductIds: initialData.allProductIds,
            selectedProducts: [],
            selectAll: false,

            applyFilters() {
                // Build URL params để reload trang (Server-side filtering)
                let params = new URLSearchParams(window.location.search);

                if (this.search) params.set('q', this.search);
                else params.delete('q');

                if (this.categoryFilter) params.set('category_id', this.categoryFilter);
                else params.delete('category_id');

                if (this.statusFilter) params.set('status', this.statusFilter);
                else params.delete('status');

                // Reset pagination to page 1 when filtering
                params.delete('page');

                window.location.search = params.toString();
            },

            toggleSelectAll() {
                // Nếu check Select All -> gán mảng selected = tất cả ID
                // Nếu bỏ check -> mảng rỗng
                this.selectedProducts = this.selectAll ? this.allProductIds : [];
            },

            bulkDelete() {
                if (this.selectedProducts.length === 0) return;

                if (confirm('Bạn có chắc chắn muốn xóa ' + this.selectedProducts.length + ' sản phẩm đã chọn?')) {
                    // Thực hiện gọi Ajax hoặc Form Submit để xóa
                    // Demo alert:
                    alert('Đang gửi yêu cầu xóa các ID: ' + this.selectedProducts.join(', '));

                    // Code xóa thật (cần tạo route bulk-delete):
                    // axios.post('/admin/products/bulk-delete', { ids: this.selectedProducts }).then(...)
                }
            }
        }));
    });
</script>
@endsection
