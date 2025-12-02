@extends('layouts.admin')
@section('title', 'Sửa sản phẩm')
@section('header', 'Cập nhật: ' . $product->name)

@section('content')
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    {{-- Basic Info --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên sản phẩm <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả chi tiết</label>
                            <textarea name="description" rows="6"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>

                    {{-- Colors Alpine --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200"
                        x-data="{ colors: {{ json_encode($product->colors ?? []) }} }">
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
                        </div>
                        <input type="hidden" name="colors" :value="JSON.stringify(colors)">

                        {{-- Hidden element để ImageEditor bên dưới có thể đọc được list colors --}}
                        <div id="available-colors" :data-colors="JSON.stringify(colors)"></div>
                    </div>

                    {{-- Specs Alpine --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200"
                        x-data="{ specs: {{ json_encode($product->metadata['specs'] ?? []) }} }">
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

                    {{-- ADVANCED IMAGE EDITOR (EDIT MODE) --}}
                    {{-- FIX: Map dữ liệu ảnh thủ công để đảm bảo có image_url --}}
                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm"
                        x-data="imageEditor(@js($product->images->map(fn($img) => ['id' => $img->id, 'image_url' => $img->image_url, 'color' => $img->color])), @js($product->colors ?? []))">

                        <label class="block text-sm font-bold text-gray-800 mb-2">Thư viện ảnh & Gán màu <span
                                class="text-red-500">*</span></label>

                        {{-- Select Button --}}
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
                                    <p class="text-sm text-gray-500">Click để thêm ảnh mới (Chọn nhiều)</p>
                                </div>
                                <input type="file" multiple accept="image/*" class="hidden" @change="handleFileSelect">
                            </label>
                        </div>

                        {{-- Hidden Inputs để gửi dữ liệu về server --}}
                        {{-- 1. Input chứa file thật (chỉ chứa các file mới) --}}
                        <input type="file" name="gallery[]" multiple class="hidden" id="realGalleryInput">
                        {{-- 2. Input JSON chứa cấu trúc sắp xếp (trộn lẫn cũ và mới) --}}
                        <input type="hidden" name="images_data" :value="JSON.stringify(imagesData)">
                        {{-- 3. Input chứa danh sách ID ảnh cũ cần xóa --}}
                        <template x-for="id in deletedIds">
                            <input type="hidden" name="deleted_image_ids[]" :value="id">
                        </template>

                        {{-- Image Grid Sortable --}}
                        <div x-show="images.length > 0">
                            <p class="text-xs text-gray-500 mb-2">Kéo thả để sắp xếp. <strong>Ảnh đầu tiên (số 1) sẽ là ảnh
                                    đại diện.</strong></p>

                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <template x-for="(img, index) in images" :key="img.uniqueId">
                                    <div class="border rounded-lg bg-gray-50 relative group p-2 flex flex-col gap-2">
                                        {{-- Image Preview --}}
                                        <div class="aspect-square rounded overflow-hidden bg-white border relative">
                                            <img :src="img.url" class="w-full h-full object-contain">

                                            {{-- Badges --}}
                                            <div x-show="index === 0"
                                                class="absolute top-0 left-0 bg-indigo-600 text-white text-[10px] font-bold px-2 py-1 z-10">
                                                Đại diện</div>
                                            <div x-show="index !== 0"
                                                class="absolute top-0 left-0 bg-gray-800/70 text-white text-[10px] font-bold px-2 py-1 z-10"
                                                x-text="index + 1"></div>
                                            <div x-show="img.isNew"
                                                class="absolute top-0 right-0 bg-green-500 text-white text-[10px] font-bold px-2 py-1 z-10">
                                                Mới</div>

                                            {{-- Actions Overlay --}}
                                            <div
                                                class="absolute inset-0 bg-black/40 hidden group-hover:flex flex-col items-center justify-center gap-1 transition z-20">
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
                                                        x-show="index < images.length - 1"
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

                                        {{-- Color Dropdown --}}
                                        <div>
                                            <label class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Màu
                                                sắc</label>
                                            <select x-model="img.color"
                                                class="w-full text-xs border-gray-300 rounded focus:ring-indigo-500 h-8">
                                                <option value="">-- Chung --</option>
                                                <template x-for="c in availableColors">
                                                    <option :value="c" :selected="img.color === c" x-text="c"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN --}}
                <div class="space-y-6">
                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá bán <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" required
                            class="w-full px-3 py-2 border rounded-lg font-bold">
                        <label class="block text-sm font-medium text-gray-700 mt-3 mb-1">Tồn kho</label>
                        <input type="number" name="quantity" value="{{ old('quantity', $product->quantity) }}"
                            class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục <span
                                class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full px-3 py-2 border rounded-lg">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm space-y-3">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Trạng thái & Hiển thị</label>
                        <div>
                            <select name="status"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Active (Đang bán)
                                </option>
                                <option value="draft" {{ $product->status === 'draft' ? 'selected' : '' }}>Draft (Bản nháp)
                                </option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between border-t pt-2">
                            <label class="text-sm text-gray-700" for="is_featured">Nổi bật</label>
                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="rounded text-indigo-600">
                        </div>
                        <div class="flex items-center justify-between border-t pt-2">
                            <label class="text-sm text-gray-700" for="special_offer">Ưu đãi</label>
                            <input type="hidden" name="special_offer" value="0">
                            <input type="checkbox" id="special_offer" name="special_offer" value="1" {{ $product->special_offer ? 'checked' : '' }} class="rounded text-indigo-600">
                        </div>
                        <div class="flex items-center justify-between border-t pt-2">
                            <label class="text-sm text-gray-700" for="online_only">Only Online</label>
                            <input type="hidden" name="online_only" value="0">
                            <input type="checkbox" id="online_only" name="online_only" value="1" {{ $product->online_only ? 'checked' : '' }} class="rounded text-indigo-600">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t">
                <a href="{{ route('admin.products.index') }}"
                    class="px-6 py-2 bg-gray-100 rounded hover:bg-gray-200 text-gray-700">Hủy</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Lưu thay
                    đổi</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('imageEditor', (existingImages, initialColors) => ({
                // Mảng chính chứa tất cả ảnh (cũ + mới)
                images: [],
                // Mảng chứa ID ảnh cũ cần xóa
                deletedIds: [],
                // Danh sách màu để bind vào dropdown
                availableColors: initialColors,
                // Bộ đếm để tạo Unique ID tạm thời cho view Alpine
                uidCounter: 0,

                init() {
                    // 1. Load ảnh cũ vào mảng
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

                    // 2. Watch sự thay đổi của input màu (ở component Colors Alpine phía trên)
                    // Vì Alpine scope cô lập, ta dùng MutationObserver hoặc event listener đơn giản
                    // Ở đây ta dùng setInterval check nhẹ hoặc giả định user save xong mới hiện màu
                    // Cách tốt nhất: User nhập màu ở trên -> Lưu -> Reload trang -> Có màu để chọn.
                    // Tuy nhiên để reactive realtime, ta có thể dùng $watch từ parent nếu gộp scope, hoặc window event.
                },

                handleFileSelect(e) {
                    const selectedFiles = Array.from(e.target.files);
                    if (selectedFiles.length === 0) return;

                    selectedFiles.forEach(file => {
                        this.uidCounter++;
                        this.images.push({
                            uniqueId: 'new_' + this.uidCounter,
                            id: null, // Ảnh mới chưa có ID DB
                            url: URL.createObjectURL(file),
                            isNew: true,
                            file: file,
                            color: '',
                        });
                    });

                    this.syncFilesInput();
                    e.target.value = ''; // Reset input để chọn lại được
                },

                move(index, direction) {
                    const newIndex = index + direction;
                    if (newIndex < 0 || newIndex >= this.images.length) return;

                    const temp = this.images[index];
                    this.images[index] = this.images[newIndex];
                    this.images[newIndex] = temp;
                    // Trigger reactivity
                    this.images = [...this.images];

                    // Không cần syncFilesInput ở đây vì thứ tự file input chỉ quan tâm file mới
                    // Thứ tự hiển thị sẽ được gửi qua JSON 'imagesData'
                },

                remove(index) {
                    const img = this.images[index];

                    // Nếu là ảnh cũ -> thêm vào danh sách xóa
                    if (!img.isNew && img.id) {
                        this.deletedIds.push(img.id);
                    }

                    this.images.splice(index, 1);

                    // Nếu là ảnh mới -> cần sync lại input file
                    if (img.isNew) {
                        this.syncFilesInput();
                    }
                },

                // Logic Sync input[type=file] chứa các file mới
                syncFilesInput() {
                    const dataTransfer = new DataTransfer();
                    this.images.forEach(img => {
                        if (img.isNew && img.file) {
                            dataTransfer.items.add(img.file);
                        }
                    });
                    document.getElementById('realGalleryInput').files = dataTransfer.files;
                },

                // Computed property trả về JSON cấu trúc để gửi lên server
                get imagesData() {
                    // Map lại mảng images hiện tại thành cấu trúc server hiểu
                    // Với ảnh mới, ta cần biết nó nằm ở index nào trong input[type=file]
                    let newFileCounter = 0;

                    return this.images.map(img => {
                        let item = {
                            id: img.id, // null nếu là mới
                            color: img.color
                        };

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
