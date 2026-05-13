@extends('layouts.admin')

@section('title', 'Tạo Mã giảm giá mới')

@section('content')
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100">
                        <div>
                            <h1 class="text-3xl font-extrabold text-gray-900">Tạo Mã Giảm Giá Mới</h1>
                            <p class="mt-2 text-sm text-gray-500">Thiết lập các chương trình khuyến mãi và ưu đãi cho khách hàng.</p>
                        </div>
                        <a href="{{ route('admin.coupons.index') }}" class="flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <i data-lucide="arrow-left" class="w-5 h-5 mr-1"></i>
                            Quay lại
                        </a>
                    </div>

                    <form action="{{ route('admin.coupons.store') }}" method="POST">
                        @csrf

                        <div class="space-y-10">
                            {{-- Thông tin cơ bản --}}
                            <section>
                                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i data-lucide="info" class="w-5 h-5 mr-2 text-indigo-500"></i>
                                    Thông tin cơ bản
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã Coupon <span class="text-red-500">*</span></label>
                                        <input type="text" name="code" value="{{ old('code') }}" required
                                            class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 uppercase px-4 py-3"
                                            placeholder="VD: WELCOME2025">
                                        @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Kênh áp dụng <span class="text-red-500">*</span></label>
                                        <select name="channel" class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-3">
                                            <option value="both" {{ old('channel') == 'both' ? 'selected' : '' }}>Tất cả (Hệ thống & Email)</option>
                                            <option value="system" {{ old('channel') == 'system' ? 'selected' : '' }}>Chỉ Hệ thống (Web)</option>
                                            <option value="email" {{ old('channel') == 'email' ? 'selected' : '' }}>Chỉ Email (Chào mừng/Tri ân)</option>
                                        </select>
                                    </div>

                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả mã giảm giá</label>
                                        <textarea name="description" rows="2"
                                            class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2"
                                            placeholder="VD: Giảm 10% cho tất cả đơn hàng từ 500k...">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </section>

                            {{-- Cấu hình giảm giá --}}
                            <section>
                                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i data-lucide="settings-2" class="w-5 h-5 mr-2 text-green-500"></i>
                                    Cấu hình giảm giá
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-2xl">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Loại giảm giá <span class="text-red-500">*</span></label>
                                        <select name="discount_type" class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-green-500 focus:border-green-500 px-4 py-3">
                                            <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Số tiền cố định (VNĐ)</option>
                                            <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá trị giảm <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <input type="number" name="discount_value" value="{{ old('discount_value') }}" required
                                                class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-green-500 focus:border-green-500 px-4 py-3"
                                                placeholder="VD: 50000 hoặc 10">
                                        </div>
                                        @error('discount_value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Đơn hàng tối thiểu</label>
                                        <input type="number" name="min_order_amount" value="{{ old('min_order_amount', 0) }}"
                                            class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-green-500 focus:border-green-500 px-4 py-3">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Giới hạn số lần dùng</label>
                                        <input type="number" name="usage_limit" value="{{ old('usage_limit') }}"
                                            class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-green-500 focus:border-green-500 px-4 py-3"
                                            placeholder="Để trống nếu không giới hạn">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày hết hạn (Expiry Date)</label>
                                        <input type="datetime-local" name="expiry_date" value="{{ old('expiry_date') }}"
                                            class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-green-500 focus:border-green-500 px-4 py-3">
                                    </div>

                                    <div class="flex items-center mt-8">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                            <span class="ml-3 text-sm font-medium text-gray-700">Kích hoạt ngay</span>
                                        </label>
                                    </div>
                                </div>
                            </section>

                            {{-- Giới hạn áp dụng --}}
                            <section x-data="{ 
                                searchCat: '', 
                                searchBrand: '', 
                                searchProduct: '',
                                selectedCats: [],
                                selectedBrands: [],
                                selectedProducts: [],
                                allCats: @js($categories->mapWithKeys(fn($c) => [$c->id => $c->name])),
                                allBrands: @js($brands->mapWithKeys(fn($b) => [$b->id => $b->name])),
                                allProducts: @js($products->mapWithKeys(fn($p) => [$p->id => $p->name])),
                                
                                filter(text, search) {
                                    return text.toLowerCase().includes(search.toLowerCase());
                                },
                                removeItem(type, id) {
                                    if(type === 'cat') this.selectedCats = this.selectedCats.filter(i => i != id);
                                    if(type === 'brand') this.selectedBrands = this.selectedBrands.filter(i => i != id);
                                    if(type === 'product') this.selectedProducts = this.selectedProducts.filter(i => i != id);
                                }
                            }">
                                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i data-lucide="filter" class="w-5 h-5 mr-2 text-rose-500"></i>
                                    Giới hạn áp dụng
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                    {{-- Danh mục --}}
                                    <div class="flex flex-col h-full">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Áp dụng cho Danh mục</label>
                                        
                                        {{-- Tags Container --}}
                                        <div class="flex flex-wrap gap-2 mb-3 min-h-[32px]">
                                            <template x-for="id in selectedCats" :key="id">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 border border-indigo-200">
                                                    <span x-text="allCats[id]"></span>
                                                    <button type="button" @click="removeItem('cat', id)" class="ml-1.5 inline-flex items-center justify-center h-4 w-4 rounded-full text-indigo-400 hover:bg-indigo-200 hover:text-indigo-600 focus:outline-none">
                                                        <i data-lucide="x" class="w-3 h-3"></i>
                                                    </button>
                                                </span>
                                            </template>
                                            <template x-if="selectedCats.length === 0">
                                                <span class="text-xs text-gray-400 italic">Chưa chọn danh mục nào</span>
                                            </template>
                                        </div>

                                        <div class="relative mb-2">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                <i data-lucide="search" class="w-4 h-4"></i>
                                            </span>
                                            <input type="text" x-model="searchCat" placeholder="Tìm danh mục..." 
                                                class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                        </div>
                                        <div class="border border-gray-200 rounded-xl p-2 h-48 overflow-y-auto space-y-1 bg-white custom-scrollbar shadow-inner">
                                            @foreach($categories as $category)
                                                <label x-show="filter('{{ addslashes($category->name) }}', searchCat)" 
                                                    class="flex items-center space-x-3 cursor-pointer hover:bg-indigo-50 p-2 rounded-lg transition-colors group">
                                                    <input type="checkbox" name="applicable_categories[]" value="{{ $category->id }}" x-model="selectedCats"
                                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                                    <span class="text-sm text-gray-600 group-hover:text-indigo-700 font-medium">{{ $category->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-2 italic">Bỏ trống nếu áp dụng cho tất cả danh mục</p>
                                    </div>

                                    {{-- Thương hiệu --}}
                                    <div class="flex flex-col h-full">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Áp dụng cho Thương hiệu</label>
                                        
                                        {{-- Tags Container --}}
                                        <div class="flex flex-wrap gap-2 mb-3 min-h-[32px]">
                                            <template x-for="id in selectedBrands" :key="id">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-800 border border-rose-200">
                                                    <span x-text="allBrands[id]"></span>
                                                    <button type="button" @click="removeItem('brand', id)" class="ml-1.5 inline-flex items-center justify-center h-4 w-4 rounded-full text-rose-400 hover:bg-rose-200 hover:text-rose-600 focus:outline-none">
                                                        <i data-lucide="x" class="w-3 h-3"></i>
                                                    </button>
                                                </span>
                                            </template>
                                            <template x-if="selectedBrands.length === 0">
                                                <span class="text-xs text-gray-400 italic">Chưa chọn thương hiệu nào</span>
                                            </template>
                                        </div>

                                        <div class="relative mb-2">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                <i data-lucide="search" class="w-4 h-4"></i>
                                            </span>
                                            <input type="text" x-model="searchBrand" placeholder="Tìm thương hiệu..." 
                                                class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                        </div>
                                        <div class="border border-gray-200 rounded-xl p-2 h-48 overflow-y-auto space-y-1 bg-white custom-scrollbar shadow-inner">
                                            @foreach($brands as $brand)
                                                <label x-show="filter('{{ addslashes($brand->name) }}', searchBrand)" 
                                                    class="flex items-center space-x-3 cursor-pointer hover:bg-rose-50 p-2 rounded-lg transition-colors group">
                                                    <input type="checkbox" name="applicable_brands[]" value="{{ $brand->id }}" x-model="selectedBrands"
                                                        class="rounded border-gray-300 text-rose-600 focus:ring-rose-500 w-4 h-4">
                                                    <span class="text-sm text-gray-600 group-hover:text-rose-700 font-medium">{{ $brand->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Sản phẩm cụ thể --}}
                                    <div class="flex flex-col h-full">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Sản phẩm cụ thể</label>
                                        
                                        {{-- Tags Container --}}
                                        <div class="flex flex-wrap gap-2 mb-3 min-h-[32px]">
                                            <template x-for="id in selectedProducts" :key="id">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <span x-text="allProducts[id]"></span>
                                                    <button type="button" @click="removeItem('product', id)" class="ml-1.5 inline-flex items-center justify-center h-4 w-4 rounded-full text-emerald-400 hover:bg-emerald-200 hover:text-emerald-600 focus:outline-none">
                                                        <i data-lucide="x" class="w-3 h-3"></i>
                                                    </button>
                                                </span>
                                            </template>
                                            <template x-if="selectedProducts.length === 0">
                                                <span class="text-xs text-gray-400 italic">Chưa chọn sản phẩm nào</span>
                                            </template>
                                        </div>

                                        <div class="relative mb-2">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                <i data-lucide="search" class="w-4 h-4"></i>
                                            </span>
                                            <input type="text" x-model="searchProduct" placeholder="Tìm sản phẩm..." 
                                                class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                        </div>
                                        <div class="border border-gray-200 rounded-xl p-2 h-48 overflow-y-auto space-y-1 bg-white custom-scrollbar shadow-inner">
                                            @foreach($products as $product)
                                                <label x-show="filter('{{ addslashes($product->name) }}', searchProduct)" 
                                                    class="flex items-center space-x-3 cursor-pointer hover:bg-emerald-50 p-2 rounded-lg transition-colors group">
                                                    <input type="checkbox" name="applicable_products[]" value="{{ $product->id }}" x-model="selectedProducts"
                                                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                                    <span class="text-sm text-gray-600 group-hover:text-emerald-700 font-medium truncate">{{ $product->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <div class="mt-12 pt-8 border-t border-gray-100 flex items-center justify-end gap-4">
                            <a href="{{ route('admin.coupons.index') }}" 
                                class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">
                                Hủy bỏ
                            </a>
                            <button type="submit" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-3 rounded-xl font-bold shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5">
                                Lưu Mã Giảm Giá
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
