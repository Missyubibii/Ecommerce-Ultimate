@extends('layouts.app')

@section('title', $product['name'])

@section('content')
    <div class="bg-gray-50 py-8" x-data="productDetail(@js($product))">
        <div class="container mx-auto px-4">

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
                <a href="{{ route('home') }}" class="hover:text-indigo-600">Trang chủ</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <a href="{{ route('category.show', $product['category']['slug']) }}"
                    class="hover:text-indigo-600">{{ $product['category']['name'] }}</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span class="font-semibold text-gray-800 truncate max-w-[200px]">{{ $product['name'] }}</span>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2">

                    {{-- Left: Gallery --}}
                    <div class="p-6 md:p-8 border-b md:border-b-0 md:border-r border-gray-100">
                        <div
                            class="relative aspect-square bg-gray-50 rounded-xl overflow-hidden mb-4 border border-gray-100 group">
                            <img :src="currentImage" :alt="product.name"
                                class="w-full h-full object-contain p-6 transition-transform duration-500 hover:scale-110 cursor-zoom-in">
                        </div>

                        {{-- Thumbnails --}}
                        <div class="grid grid-cols-5 gap-3">
                            {{-- Main Image Thumb --}}
                            <div @click="currentImage = product.image_url"
                                class="aspect-square rounded-lg border overflow-hidden cursor-pointer hover:border-indigo-500 transition"
                                :class="currentImage === product.image_url ? 'border-indigo-500 ring-1 ring-indigo-500' : 'border-gray-200'">
                                <img :src="product.image_url" class="w-full h-full object-cover">
                            </div>
                            {{-- Gallery Thumbs --}}
                            <template x-for="img in product.images" :key="img.id">
                                <div @click="currentImage = img.image_url"
                                    class="aspect-square rounded-lg border overflow-hidden cursor-pointer hover:border-indigo-500 transition"
                                    :class="currentImage === img.image_url ? 'border-indigo-500 ring-1 ring-indigo-500' : 'border-gray-200'">
                                    <img :src="img.image_url" class="w-full h-full object-cover">
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Right: Info --}}
                    <div class="p-6 md:p-8">
                        <div class="mb-6">
                            <span
                                class="text-indigo-600 font-bold text-xs uppercase tracking-wider bg-indigo-50 px-2 py-1 rounded">{{ $product['category']['name'] }}</span>
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mt-3 mb-2">{{ $product['name'] }}</h1>
                            <p class="text-sm text-gray-500">SKU: <span
                                    class="font-mono text-gray-700">{{ $product['sku'] }}</span></p>
                        </div>

                        <div class="mb-8 pb-8 border-b border-gray-100">
                            <div class="flex items-end gap-3 mb-4">
                                <span
                                    class="text-3xl font-bold text-indigo-600">{{ number_format($product['price'], 0, ',', '.') }}đ</span>
                                @if($product['cost_price'] > $product['price'])
                                    <span
                                        class="text-lg text-gray-400 line-through mb-1">{{ number_format($product['cost_price'], 0, ',', '.') }}đ</span>
                                    <span class="text-sm font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded mb-1.5">
                                        -{{ round((($product['cost_price'] - $product['price']) / $product['cost_price']) * 100) }}%
                                    </span>
                                @endif
                            </div>
                            <p class="text-gray-600 leading-relaxed">
                                {{ $product['short_description'] ?? 'Đang cập nhật mô tả...' }}</p>
                        </div>

                        {{-- Add to Cart Actions --}}
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center border border-gray-300 rounded-lg">
                                    <button @click="quantity > 1 ? quantity-- : null"
                                        class="px-4 py-2 hover:bg-gray-100 text-gray-600 font-bold">-</button>
                                    <input type="text" x-model="quantity" readonly
                                        class="w-12 text-center border-none focus:ring-0 font-bold text-gray-800">
                                    <button @click="quantity++"
                                        class="px-4 py-2 hover:bg-gray-100 text-gray-600 font-bold">+</button>
                                </div>
                                <div class="text-sm text-gray-500">
                                    Còn lại: <span class="font-bold text-gray-800">{{ $product['quantity'] }}</span> sản
                                    phẩm
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <button @click="addToCart()"
                                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-xl font-bold shadow-lg shadow-indigo-200 transition flex items-center justify-center gap-2">
                                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                                    Thêm vào giỏ
                                </button>
                                <button
                                    class="w-12 flex items-center justify-center border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-red-500 transition">
                                    <i data-lucide="heart" class="w-6 h-6"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Specs --}}
                        @if(!empty($product['metadata']['specs']))
                            <div class="mt-8 pt-8 border-t border-gray-100">
                                <h3 class="font-bold text-gray-900 mb-3">Thông số kỹ thuật</h3>
                                <div class="grid grid-cols-2 gap-y-2 text-sm">
                                    @foreach($product['metadata']['specs'] as $spec)
                                        <div class="text-gray-500">{{ $spec['key'] }}</div>
                                        <div class="font-medium text-gray-800">{{ $spec['value'] }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Description Tab --}}
            <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Mô tả chi tiết</h3>
                <div class="prose max-w-none text-gray-600">
                    {!! nl2br(e($product['description'])) !!}
                </div>
            </div>

            {{-- Related Products --}}
            @if($related->isNotEmpty())
                <div class="mt-12">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Sản phẩm liên quan</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        @foreach($related as $item)
                            <div
                                class="bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-lg transition group">
                                <a href="{{ route('product.show', $item->slug) }}" class="block relative pt-[100%] bg-white">
                                    <img src="{{ $item->image_url }}"
                                        class="absolute top-0 left-0 w-full h-full object-contain p-4 group-hover:scale-110 transition">
                                </a>
                                <div class="p-4">
                                    <h4 class="text-sm font-medium text-gray-800 line-clamp-2 mb-2">
                                        <a href="{{ route('product.show', $item->slug) }}">{{ $item->name }}</a>
                                    </h4>
                                    <div class="font-bold text-indigo-600">{{ number_format($item->price, 0, ',', '.') }}đ</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

    <script>
        function productDetail(productData) {
            return {
                product: productData,
                currentImage: productData.image_url,
                quantity: 1,

                async addToCart() {
                    try {
                        const res = await axios.post('{{ route("cart.add") }}', {
                            product_id: this.product.id,
                            quantity: this.quantity
                        });
                        if (res.data.success) {
                            alert('Đã thêm vào giỏ hàng thành công!');
                            window.location.reload();
                        }
                    } catch (e) {
                        alert('Lỗi: ' + (e.response?.data?.message || 'Không thể thêm vào giỏ'));
                    }
                }
            }
        }
    </script>
@endsection
