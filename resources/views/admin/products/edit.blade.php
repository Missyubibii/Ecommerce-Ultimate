@extends('layouts.admin')
@section('title', 'Chỉnh sửa sản phẩm')
@section('header', 'Cập nhật sản phẩm')

@section('content')
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Cột Trái: Thông tin chính --}}
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tên sản phẩm</label>
                                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                                <textarea name="description" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Dữ liệu & Giá</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Giá bán</label>
                                <input type="number" name="price" value="{{ old('price', $product->price) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Giá vốn (nhập)</label>
                                <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">SKU (Mã SP)</label>
                                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Số lượng tồn</label>
                                <input type="number" name="quantity" value="{{ old('quantity', $product->quantity) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cột Phải: Ảnh & Phân loại --}}
                <div class="space-y-6">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Trạng thái</h3>
                        <select name="status" class="block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="draft" {{ $product->status == 'draft' ? 'selected' : '' }}>Bản nháp (Draft)
                            </option>
                            <option value="active" {{ $product->status == 'active' ? 'selected' : '' }}>Hoạt động (Active)
                            </option>
                        </select>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Danh mục</label>
                            <select name="category_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">-- Chọn --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Thay đổi ảnh đại diện</h3>
                        <input type="file" name="image"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                        @if($product->image)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                class="mt-2 w-full h-32 object-cover rounded">
                        @endif
                    </div>

                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Thêm ảnh vào Gallery</h3>
                        <input type="file" name="gallery[]" multiple class="block w-full text-sm text-gray-500" />
                    </div>
                </div>

                {{-- 1. MÔ TẢ NGẮN --}}
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Mô tả ngắn (SEO)</label>
                    <textarea name="short_description" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('short_description', $product->short_description) }}</textarea>
                </div>

                {{-- 2. THÔNG SỐ KỸ THUẬT (DYNAMIC ALPINEJS) --}}
                <div class="col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-200"
                    x-data="{ specs: {{ json_encode($product->metadata['specs'] ?? [['key' => '', 'value' => '']]) }} }">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700">Thông số kỹ thuật</label>
                        <button type="button" @click="specs.push({key: '', value: ''})"
                            class="text-sm text-indigo-600 hover:underline">+ Thêm dòng</button>
                    </div>

                    <template x-for="(spec, index) in specs" :key="index">
                        <div class="flex gap-2 mb-2">
                            <input type="text" :name="`specs[${index}][key]`" x-model="spec.key" placeholder="Tên"
                                class="w-1/3 rounded-md border-gray-300 shadow-sm text-sm">
                            <input type="text" :name="`specs[${index}][value]`" x-model="spec.value" placeholder="Giá trị"
                                class="w-2/3 rounded-md border-gray-300 shadow-sm text-sm">
                            <button type="button" @click="specs = specs.filter((_, i) => i !== index)"
                                class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.products.index') }}"
                    class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200 text-gray-700">Hủy</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Cập nhật sản
                    phẩm</button>
            </div>
        </form>

        {{-- KHU VỰC SẮP XẾP ẢNH (MỚI) --}}
        @if($product->images->count() > 0)
            <div class="bg-white shadow rounded-lg p-6 mt-6"
                x-data="imageGallery({{ $product->id }}, {{ $product->images->sortBy('sort_order')->values()->map(function($img) { $img->src = $img->image_url; return $img; }) }})">

                <h3 class="text-lg font-medium text-gray-900 mb-2">Quản lý thư viện ảnh</h3>
                <p class="text-sm text-gray-500 mb-4">
                    💡 Kéo ảnh lên vị trí đầu tiên (số 1) để đặt làm <strong>Ảnh đại diện</strong>.
                </p>

                <div x-ref="galleryGrid" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <template x-for="(img, index) in images" :key="img.id">
                        <div :data-id="img.id"
                            class="group relative border rounded-lg overflow-hidden cursor-move bg-gray-50 hover:shadow-md transition">
                            <div class="aspect-square">
                                <img :src="img.src" class="w-full h-full object-cover">
                            </div>
                            <div x-show="index === 0"
                                class="absolute top-2 left-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded shadow">
                                Ảnh đại diện
                            </div>
                            <div x-show="index !== 0"
                                class="absolute top-2 left-2 bg-gray-800/70 text-white text-xs font-bold px-2 py-1 rounded"
                                x-text="'#' + (index + 1)"></div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- SCRIPT SORTABLE --}}
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('imageGallery', (productId, initialImages) => ({
                        images: initialImages,
                        init() {
                            new Sortable(this.$refs.galleryGrid, {
                                animation: 150,
                                ghostClass: 'bg-indigo-100',
                                onEnd: (evt) => { this.updateOrder(); }
                            });
                        },
                        updateOrder() {
                            let orderedIds = Array.from(this.$refs.galleryGrid.children).map(el => el.getAttribute('data-id'));
                            this.images = orderedIds.map(id => this.images.find(img => img.id == id));
                            axios.patch(`/admin/products/${productId}/reorder-images`, { ids: orderedIds })
                                .then(() => console.log('Đã cập nhật thứ tự!'))
                                .catch(() => alert('Lỗi cập nhật.'));
                        }
                    }));
                });
            </script>
        @endif
    </div>
@endsection
