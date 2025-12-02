@extends('layouts.admin')
@section('title', 'Thêm sản phẩm mới')
@section('header', 'Tạo sản phẩm mới')

@section('content')
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm p-6">
        <form id="productForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- CỘT TRÁI (Lớn) --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên sản phẩm <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả chi tiết</label>
                            <textarea name="description" rows="6"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    {{-- Colors Alpine --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200" x-data="{ colors: [] }">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-gray-800 uppercase">Màu sắc sản phẩm</h3>
                            <div class="flex gap-2">
                                <input type="text" placeholder="Nhập màu..." class="px-2 py-1 border rounded text-sm"
                                    @keydown.enter.prevent="$el.nextElementSibling.click()">
                                <button type="button"
                                    @click="let val = $el.previousElementSibling.value.trim(); if(val && !colors.includes(val)) { colors.push(val); $el.previousElementSibling.value = ''; }"
                                    class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded hover:bg-indigo-200 font-bold">+
                                    Thêm</button>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-2">
                            <template x-for="(color, index) in colors" :key="index">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white border border-gray-300 text-gray-800 shadow-sm">
                                    <span x-text="color"></span>
                                    <button type="button" @click="colors = colors.filter((_, i) => i !== index)"
                                        class="ml-2 text-red-500 hover:text-red-700 font-bold">×</button>
                                </span>
                            </template>
                            <div x-show="colors.length === 0" class="text-sm text-gray-400 italic">Chưa có màu nào được
                                thêm.</div>
                        </div>
                        <input type="hidden" name="colors" :value="JSON.stringify(colors)">
                    </div>

                    {{-- Specs Alpine --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200" x-data="{ specs: [] }">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-gray-800 uppercase">Thông số kỹ thuật</h3>
                            <button type="button" @click="specs.push({key: '', value: ''})"
                                class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-200">+
                                Thêm</button>
                        </div>
                        <template x-for="(spec, index) in specs" :key="index">
                            <div class="flex gap-2 mb-2">
                                <input type="text" placeholder="Tên" x-model="spec.key"
                                    class="w-1/3 px-3 py-2 border rounded text-sm">
                                <input type="text" placeholder="Giá trị" x-model="spec.value"
                                    class="w-2/3 px-3 py-2 border rounded text-sm">
                                <button type="button" @click="specs = specs.filter((_, i) => i !== index)"
                                    class="text-red-500 px-2">X</button>
                            </div>
                        </template>
                        <input type="hidden" name="specifications" :value="JSON.stringify(specs)">
                    </div>
                </div>

                {{-- CỘT PHẢI (Nhỏ) --}}
                <div class="space-y-6">
                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá bán <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="price" value="{{ old('price') }}" required
                            class="w-full px-3 py-2 border rounded-lg font-bold">
                        <label class="block text-sm font-medium text-gray-700 mt-3 mb-1">Tồn kho</label>
                        <input type="number" name="quantity" value="{{ old('quantity', 0) }}"
                            class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục <span
                                class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="">-- Chọn --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Single Input Sortable --}}
                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm" x-data="imageUploader()">
                        <label class="block text-sm font-bold text-gray-800 mb-2">Hình ảnh sản phẩm <span
                                class="text-red-500">*</span></label>

                        {{-- Nút chọn ảnh --}}
                        <div class="mb-4">
                            <label
                                class="cursor-pointer flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-indigo-300 rounded-lg hover:bg-indigo-50 transition">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 text-indigo-500 mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                        </path>
                                    </svg>
                                    <p class="text-sm text-gray-500">Click để chọn ảnh (Chọn nhiều)</p>
                                </div>
                                <input type="file" multiple accept="image/*" class="hidden" @change="handleFileSelect">
                            </label>
                            {{-- Input ẩn để form submit nhận dữ liệu --}}
                            <input type="file" name="gallery[]" multiple class="hidden" id="realGalleryInput">
                        </div>

                        {{-- Preview & Sort --}}
                        <div x-show="files.length > 0">
                            <p class="text-xs text-gray-500 mb-2">Kéo thả hoặc dùng nút mũi tên để sắp xếp. <strong>Ảnh đầu
                                    tiên (số 1) sẽ là ảnh đại diện.</strong></p>
                            <div class="grid grid-cols-3 gap-2">
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="relative group border rounded-lg overflow-hidden bg-gray-100 aspect-square">
                                        <img :src="file.url" class="w-full h-full object-cover">

                                        {{-- Badge ảnh đại diện --}}
                                        <div x-show="index === 0"
                                            class="absolute top-0 left-0 bg-indigo-600 text-white text-[10px] font-bold px-2 py-1 z-10">
                                            Đại diện
                                        </div>
                                        <div x-show="index !== 0"
                                            class="absolute top-0 left-0 bg-gray-800/70 text-white text-[10px] font-bold px-2 py-1 z-10"
                                            x-text="index + 1"></div>

                                        {{-- Actions Overlay --}}
                                        <div
                                            class="absolute inset-0 bg-black/40 hidden group-hover:flex flex-col items-center justify-center gap-1 transition">
                                            <div class="flex gap-1">
                                                <button type="button" @click="move(index, -1)" x-show="index > 0"
                                                    class="p-1 bg-white rounded text-gray-800 hover:text-indigo-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                    </svg>
                                                </button>
                                                <button type="button" @click="move(index, 1)"
                                                    x-show="index < files.length - 1"
                                                    class="p-1 bg-white rounded text-gray-800 hover:text-indigo-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <button type="button" @click="remove(index)"
                                                class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 mt-1">Xóa</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm space-y-3">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Trạng thái & Hiển thị</label>
                        <div>
                            <select name="status"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="active">Active (Đang bán)</option>
                                <option value="draft">Draft (Bản nháp)</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between border-t pt-2">
                            <label class="text-sm text-gray-700" for="is_featured">Nổi bật</label>
                            <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                class="rounded text-indigo-600">
                        </div>
                        <div class="flex items-center justify-between border-t pt-2">
                            <label class="text-sm text-gray-700" for="special_offer">Ưu đãi</label>
                            <input type="checkbox" id="special_offer" name="special_offer" value="1"
                                class="rounded text-indigo-600">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t">
                <a href="{{ route('admin.products.index') }}"
                    class="px-6 py-2 bg-gray-100 rounded hover:bg-gray-200 text-gray-700">Hủy</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Lưu sản
                    phẩm</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('imageUploader', () => ({
                files: [], // Array chứa objects { file: File, url: String }

                handleFileSelect(e) {
                    const selectedFiles = Array.from(e.target.files);
                    if (selectedFiles.length === 0) return;

                    selectedFiles.forEach(file => {
                        this.files.push({
                            file: file,
                            url: URL.createObjectURL(file)
                        });
                    });

                    this.updateInput();
                    // Reset value để có thể chọn lại cùng file
                    e.target.value = '';
                },

                move(index, direction) {
                    const newIndex = index + direction;
                    if (newIndex < 0 || newIndex >= this.files.length) return;

                    // Swap logic
                    const temp = this.files[index];
                    this.files[index] = this.files[newIndex];
                    this.files[newIndex] = temp;

                    // Re-assign để Alpine detect change mảng
                    this.files = [...this.files];
                    this.updateInput();
                },

                remove(index) {
                    this.files.splice(index, 1);
                    this.updateInput();
                },

                updateInput() {
                    //Dùng DataTransfer để tạo FileList mới cho input ẩn
                    const dataTransfer = new DataTransfer();
                    this.files.forEach(item => {
                        dataTransfer.items.add(item.file);
                    });

                    const input = document.getElementById('realGalleryInput');
                    input.files = dataTransfer.files;
                }
            }));
        });
    </script>
@endsection
