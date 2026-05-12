@extends('layouts.admin')
@section('title', 'Chỉnh sửa sản phẩm')
@section('header', 'Cập nhật: ' . $product->name)

@section('content')
<div class="max-w-[1440px] mx-auto pb-20">
    <form id="productForm" action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Header Actions --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Chỉnh sửa sản phẩm</h1>
                <p class="text-sm text-gray-500">ID: <span class="font-mono text-indigo-600">#{{ $product->id }}</span> — Cập nhật lần cuối: {{ $product->updated_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.products.index') }}" 
                    class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl font-bold hover:bg-gray-50 transition-all flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Hủy bỏ
                </a>
                <button type="submit" 
                    class="px-8 py-2.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Cập nhật ngay
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- CỘT CHÍNH (Bên trái - 2/3) --}}
            <div class="lg:col-span-8 space-y-8">
                
                {{-- Khối 1: Thông tin cơ bản --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6">
                    <div class="flex items-center gap-3 border-b pb-4">
                        <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Thông tin cơ bản</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tên sản phẩm <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Slug (Đường dẫn)</label>
                            <input type="text" name="slug" value="{{ old('slug', $product->slug) }}"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mã model (Manufacturer)</label>
                            <input type="text" name="model_code" value="{{ old('model_code', $product->model_code) }}"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mô tả ngắn</label>
                        <textarea name="short_description" rows="3" 
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none transition-all">{{ old('short_description', $product->short_description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mô tả chi tiết</label>
                        <textarea name="description" rows="12" id="editor"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none transition-all">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>

                {{-- Khối 2: Thông số & Biến thể --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-8" x-data="{ availableColors: @js($product->colors ?? []) }">
                    <div class="flex items-center gap-3 border-b pb-4">
                        <div class="p-2 bg-purple-50 rounded-lg text-purple-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Thông số kỹ thuật & Biến thể</h3>
                    </div>

                    {{-- Colors Alpine --}}
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider">Bảng màu sản phẩm</label>
                            <div class="flex gap-2">
                                <input type="text" placeholder="Nhập màu..." class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white outline-none focus:ring-2 focus:ring-indigo-500"
                                    @keydown.enter.prevent="let val = $el.value.trim(); if(val && !availableColors.includes(val)) { availableColors.push(val); $el.value = ''; }">
                                <button type="button"
                                    @click="let val = $el.previousElementSibling.value.trim(); if(val && !availableColors.includes(val)) { availableColors.push(val); $el.previousElementSibling.value = ''; }"
                                    class="text-xs bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 font-bold transition-all shadow-md">+ Thêm</button>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3 p-4 bg-gray-50 rounded-xl border border-dashed border-gray-300 min-h-[60px]">
                            <template x-for="(color, index) in availableColors" :key="index">
                                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-bold bg-white border border-gray-200 text-gray-700 shadow-sm animate-in fade-in zoom-in duration-200">
                                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                    <span x-text="color"></span>
                                    <button type="button" @click="availableColors = availableColors.filter((_, i) => i !== index)" class="ml-1 text-red-400 hover:text-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    </button>
                                </span>
                            </template>
                        </div>
                        <input type="hidden" name="colors" :value="JSON.stringify(availableColors)">
                    </div>

                    <hr class="border-gray-100">

                    {{-- Specs Alpine --}}
                    <div class="space-y-4" x-data="{ specs: @js($product->metadata['specs'] ?? []) }">
                        <div class="flex justify-between items-center">
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider">Chi tiết cấu hình</label>
                            <button type="button" @click="specs.push({key: '', value: ''})"
                                class="text-xs bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 font-bold shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Thêm cấu hình
                            </button>
                        </div>
                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-[10px] uppercase font-bold text-gray-500">
                                    <tr>
                                        <th class="px-4 py-3">Tên thông số</th>
                                        <th class="px-4 py-3 border-l">Giá trị</th>
                                        <th class="px-4 py-3 w-10 text-center border-l">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(spec, index) in specs" :key="index">
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="p-2">
                                                <input type="text" x-model="spec.key" class="w-full px-3 py-2 bg-transparent border-none focus:ring-1 focus:ring-indigo-500 rounded-lg outline-none">
                                            </td>
                                            <td class="p-2 border-l">
                                                <input type="text" x-model="spec.value" class="w-full px-3 py-2 bg-transparent border-none focus:ring-1 focus:ring-indigo-500 rounded-lg outline-none">
                                            </td>
                                            <td class="p-2 text-center border-l">
                                                <button type="button" @click="specs.splice(index, 1)" class="text-red-400 hover:text-red-600">
                                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="specifications" :value="JSON.stringify(specs)">
                    </div>
                </div>

                {{-- Khối 3: Thư viện ảnh nâng cao --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6">
                    <div class="flex items-center gap-3 border-b pb-4">
                        <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Thư viện ảnh & Phân loại màu sắc</h3>
                    </div>

                    <div x-data="imageEditor(@js($product->images->map(fn($img) => ['id' => $img->id, 'image_url' => $img->image_url, 'color' => $img->color])), @js($product->colors ?? []))">
                        <div class="mb-6">
                            <label class="cursor-pointer flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-indigo-200 rounded-2xl hover:bg-indigo-50/50 hover:border-indigo-400 transition-all duration-300 group">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <div class="p-3 bg-indigo-50 rounded-full mb-3 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <p class="text-sm font-bold text-gray-600 uppercase tracking-wide">Thêm ảnh mới vào thư viện</p>
                                </div>
                                <input type="file" multiple accept="image/*" class="hidden" @change="handleFileSelect">
                            </label>
                            
                            <input type="file" name="gallery[]" multiple class="hidden" id="realGalleryInput">
                            <input type="hidden" name="images_data" :value="JSON.stringify(imagesData)">
                            <template x-for="id in deletedIds">
                                <input type="hidden" name="deleted_image_ids[]" :value="id">
                            </template>
                        </div>

                        <div x-show="images.length > 0" class="animate-in fade-in duration-500">
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                                <template x-for="(img, index) in images" :key="img.uniqueId">
                                    <div class="border border-gray-100 rounded-2xl overflow-hidden bg-gray-50 flex flex-col shadow-sm hover:shadow-md transition-all group relative">
                                        {{-- Image Wrap --}}
                                        <div class="aspect-square bg-white border-b relative overflow-hidden">
                                            <img :src="img.url" class="w-full h-full object-contain p-2">
                                            
                                            {{-- Badge Trạng thái --}}
                                            <div x-show="index === 0" class="absolute top-2 left-2 bg-indigo-600 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-lg z-10 animate-pulse">ĐẠI DIỆN</div>
                                            <div x-show="img.isNew" class="absolute top-2 right-2 bg-green-500 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-lg z-10">MỚI</div>
                                            
                                            {{-- Overlay Actions --}}
                                            <div class="absolute inset-0 bg-indigo-900/40 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center gap-2 transition-all duration-300 z-20">
                                                <div class="flex gap-2">
                                                    <button type="button" @click="move(index, -1)" x-show="index > 0" class="p-2 bg-white rounded-lg text-gray-800 hover:text-indigo-600 transform hover:scale-110">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                    <button type="button" @click="move(index, 1)" x-show="index < images.length - 1" class="p-2 bg-white rounded-lg text-gray-800 hover:text-indigo-600 transform hover:scale-110">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                </div>
                                                <button type="button" @click="remove(index)" class="px-4 py-1.5 bg-red-500 text-white text-[10px] font-bold rounded-lg shadow-xl hover:bg-red-600">XÓA</button>
                                            </div>
                                        </div>

                                        {{-- Color Mapping --}}
                                        <div class="p-3 space-y-1">
                                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Gán màu sắc</label>
                                            <select x-model="img.color" class="w-full text-xs font-bold border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 outline-none py-1.5 transition-all">
                                                <option value="">Chung (Tất cả)</option>
                                                <template x-for="c in availableColors">
                                                    <option :value="c" x-text="c"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CỘT PHỤ (Bên phải - 1/3) --}}
            <div class="lg:col-span-4 space-y-8 sticky top-6">
                
                {{-- Khối Giá & Kho --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 space-y-5">
                    <div class="flex items-center gap-3 border-b pb-3">
                        <div class="p-2 bg-green-50 rounded-lg text-green-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <h3 class="text-md font-bold text-gray-900">Giá & Tồn kho</h3>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Giá bán niêm yết (VNĐ)</label>
                            <input type="number" name="price" value="{{ old('price', $product->price) }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-xl font-black text-green-600">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 text-gray-400">Giá thị trường (Gạch bỏ)</label>
                            <input type="number" name="market_price" value="{{ old('market_price', $product->market_price) }}"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-400 outline-none text-gray-500 decoration-red-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mã SKU</label>
                                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-mono text-xs">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tồn kho</label>
                                <input type="number" name="quantity" value="{{ old('quantity', $product->quantity) }}" required
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Khối Phân loại --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 space-y-5">
                    <div class="flex items-center gap-3 border-b pb-3">
                        <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <h3 class="text-md font-bold text-gray-900">Phân loại & Thương hiệu</h3>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Danh mục</label>
                            <select name="category_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Searchable Brand --}}
                        <div x-data="{ 
                            open: false, 
                            search: '', 
                            selectedId: '{{ $product->brand_id }}', 
                            selectedName: '{{ $product->brand->name ?? '-- Chọn thương hiệu --' }}',
                            brands: @js($brands),
                            showAddModal: false,
                            newBrandName: '',
                            newBrandLogoUrl: '',
                            get filteredBrands() {
                                return this.brands.filter(b => b.name.toLowerCase().includes(this.search.toLowerCase()))
                            }
                        }">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Thương hiệu</label>
                            <div class="relative">
                                <button type="button" @click="open = !open" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-left flex justify-between items-center focus:ring-2 focus:ring-indigo-500">
                                    <span x-text="selectedName" class="font-bold text-gray-800"></span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <input type="hidden" name="brand_id" :value="selectedId">

                                {{-- Dropdown --}}
                                <div x-show="open" @click.away="open = false" x-transition
                                    class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-2xl max-h-64 overflow-hidden flex flex-col">
                                    <div class="p-3 border-b bg-gray-50">
                                        <input type="text" x-model="search" placeholder="Tìm tên hãng..." 
                                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div class="overflow-y-auto py-1">
                                        <template x-for="brand in filteredBrands" :key="brand.id">
                                            <div @click="selectedId = brand.id; selectedName = brand.name; open = false" 
                                                class="px-4 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm transition-colors"
                                                :class="selectedId == brand.id ? 'bg-indigo-100 font-black text-indigo-700' : 'text-gray-600'"
                                                x-text="brand.name"></div>
                                        </template>
                                    </div>
                                    <div class="p-2 border-t bg-gray-50">
                                        <button type="button" @click="showAddModal = true; open = false" 
                                            class="w-full py-2 text-xs bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700">
                                            + Tạo mới thương hiệu
                                        </button>
                                    </div>
                                </div>

                                {{-- Modal Thêm Nhanh --}}
                                <div x-show="showAddModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-transition>
                                    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-sm" @click.away="showAddModal = false">
                                        <h3 class="text-xl font-black mb-6 text-indigo-900">Thêm thương hiệu mới</h3>
                                        <div class="space-y-5">
                                            <div>
                                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Tên hãng</label>
                                                <input type="text" x-model="newBrandName" placeholder="VD: Dell, HP..." 
                                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Logo</label>
                                                <div class="flex items-center gap-4">
                                                    <div class="w-16 h-16 border-2 border-dashed border-gray-100 rounded-xl flex items-center justify-center overflow-hidden bg-gray-50 shrink-0">
                                                        <template x-if="!newBrandLogoUrl">
                                                            <svg class="w-6 h-6 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        </template>
                                                        <template x-if="newBrandLogoUrl">
                                                            <img :src="newBrandLogoUrl" class="w-full h-full object-contain p-1">
                                                        </template>
                                                    </div>
                                                    <label class="flex-1">
                                                        <span class="inline-block px-3 py-1.5 bg-gray-100 text-gray-600 text-[10px] font-bold rounded-lg cursor-pointer hover:bg-gray-200 transition">Chọn ảnh logo</span>
                                                        <input type="file" name="new_brand_logo" class="hidden" @change="const file = $el.files[0]; if(file) newBrandLogoUrl = URL.createObjectURL(file)">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex justify-end gap-3 mt-8 pt-4 border-t">
                                            <button type="button" @click="showAddModal = false" class="px-5 py-2 text-sm font-bold text-gray-500">Đóng</button>
                                            <button type="button" @click="if(newBrandName) { const id = 'NEW:' + newBrandName; brands.push({id: id, name: newBrandName}); selectedId = id; selectedName = newBrandName; showAddModal = false; newBrandName = ''; }"
                                                class="px-6 py-2 bg-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-indigo-100">Xác nhận</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Xuất xứ</label>
                            <input type="text" name="origin" value="{{ old('origin', $product->origin) }}" placeholder="VD: Nhật Bản..."
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                    </div>
                </div>

                {{-- Khối Trạng thái --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 space-y-6">
                    <div class="flex items-center gap-3 border-b pb-3">
                        <div class="p-2 bg-orange-50 rounded-lg text-orange-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <h3 class="text-md font-bold text-gray-900">Niêm yết & Trạng thái</h3>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Hiển thị sản phẩm</label>
                            <select name="status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800">
                                <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Công khai (Active)</option>
                                <option value="draft" {{ $product->status === 'draft' ? 'selected' : '' }}>Bản nháp (Draft)</option>
                            </select>
                        </div>

                        <div x-data="{ 
                            conditions: ['Mới (New)', 'Hàng trưng bày', 'Like New 99%', 'Đã qua sử dụng', 'Hàng tân trang (Refurbished)'],
                            selectedCondition: '{{ old('condition', $product->condition ?? 'Mới (New)') }}',
                            showAddInput: false,
                            newCondition: '',
                            init() {
                                if (this.selectedCondition && !this.conditions.includes(this.selectedCondition)) {
                                    this.conditions.push(this.selectedCondition);
                                }
                            }
                        }">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tình trạng máy</label>
                            <div class="space-y-2">
                                <select name="condition" x-model="selectedCondition" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
                                    <template x-for="cond in conditions">
                                        <option :value="cond" x-text="cond"></option>
                                    </template>
                                </select>
                                <button type="button" @click="showAddInput = !showAddInput" class="text-[10px] text-indigo-600 font-bold hover:underline">+ Tình trạng máy khác</button>
                                
                                <div x-show="showAddInput" x-transition class="flex gap-2 p-2 bg-indigo-50 rounded-xl border border-indigo-100">
                                    <input type="text" x-model="newCondition" placeholder="VD: Lỗi nhẹ..." class="flex-1 px-3 py-1.5 text-xs bg-white border border-indigo-200 rounded-lg outline-none">
                                    <button type="button" @click="if(newCondition) { if(!conditions.includes(newCondition)) conditions.push(newCondition); selectedCondition = newCondition; newCondition = ''; showAddInput = false; }"
                                        class="px-4 py-1.5 bg-indigo-600 text-white text-[10px] font-black rounded-lg shadow-md">LƯU</button>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 pt-2">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="text-sm font-medium text-gray-700">Sản phẩm nổi bật</span>
                                <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded-md focus:ring-indigo-500">
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="text-sm font-medium text-gray-700">Ưu đãi đặc biệt</span>
                                <input type="checkbox" name="special_offer" value="1" {{ $product->special_offer ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded-md focus:ring-indigo-500">
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="text-sm font-medium text-gray-700">Chỉ bán Online</span>
                                <input type="checkbox" name="online_only" value="1" {{ $product->online_only ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded-md focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Panel --}}
                <div class="bg-indigo-900 p-6 rounded-2xl shadow-xl shadow-indigo-100 space-y-4">
                    <button type="submit" class="w-full py-4 bg-white text-indigo-900 rounded-xl font-black hover:bg-gray-100 shadow-xl active:scale-95 transition-all flex items-center justify-center gap-2 uppercase tracking-widest">
                        <svg class="w-5 h-5 font-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Cập nhật ngay
                    </button>
                    <button type="button" onclick="if(confirm('Bạn có chắc muốn xóa sản phẩm này?')) { /* Logic delete */ }" class="w-full py-2.5 text-red-300 text-xs font-bold hover:text-red-400 transition-all text-center">
                        Xóa sản phẩm vĩnh viễn
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Scripts --}}
<script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor', {
        height: 450,
        uiColor: '#F8FAFC',
    });

    document.addEventListener('alpine:init', () => {
        Alpine.data('imageEditor', (existingImages, initialColors) => ({
            images: [],
            deletedIds: [],
            availableColors: initialColors,
            uidCounter: 0,

            init() {
                if (existingImages && Array.isArray(existingImages)) {
                    existingImages.forEach(img => {
                        this.images.push({
                            uniqueId: 'old_' + img.id,
                            id: img.id,
                            url: img.image_url,
                            isNew: false,
                            file: null,
                            color: img.color || '',
                        });
                    });
                }
            },

            handleFileSelect(e) {
                const selectedFiles = Array.from(e.target.files);
                if (selectedFiles.length === 0) return;
                selectedFiles.forEach(file => {
                    this.uidCounter++;
                    this.images.push({
                        uniqueId: 'new_' + this.uidCounter,
                        id: null,
                        url: URL.createObjectURL(file),
                        isNew: true,
                        file: file,
                        color: '',
                    });
                });
                this.syncFilesInput();
                e.target.value = '';
            },

            move(index, direction) {
                const newIndex = index + direction;
                if (newIndex < 0 || newIndex >= this.images.length) return;
                const temp = this.images[index];
                this.images[index] = this.images[newIndex];
                this.images[newIndex] = temp;
                this.images = [...this.images];
            },

            remove(index) {
                const img = this.images[index];
                if (!img.isNew && img.id) {
                    this.deletedIds.push(img.id);
                }
                this.images.splice(index, 1);
                if (img.isNew) this.syncFilesInput();
            },

            syncFilesInput() {
                const dataTransfer = new DataTransfer();
                this.images.forEach(img => {
                    if (img.isNew && img.file) dataTransfer.items.add(img.file);
                });
                document.getElementById('realGalleryInput').files = dataTransfer.files;
            },

            get imagesData() {
                let newFileCounter = 0;
                return this.images.map(img => {
                    let item = { id: img.id, color: img.color };
                    if (img.isNew) {
                        item.new_file_index = newFileCounter;
                        newFileCounter++;
                    }
                    return item;
                });
            }
        }));
    });
</script>
@endsection
