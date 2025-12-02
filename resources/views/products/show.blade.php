@extends('layouts.app')

@section('title', $product['name'])

@section('content')
    <div class="bg-gray-50 py-8 min-h-screen" x-data="productDetail(@js($product))">
        <div class="container mx-auto px-4">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6 overflow-x-auto whitespace-nowrap pb-2">
                <a href="{{ route('home') }}" class="hover:text-indigo-600 transition">Trang chủ</a>
                <i data-lucide="chevron-right" class="w-4 h-4 shrink-0"></i>
                <a href="{{ route('category.show', $product['category']['slug']) }}"
                    class="hover:text-indigo-600 transition">{{ $product['category']['name'] }}</a>
                <i data-lucide="chevron-right" class="w-4 h-4 shrink-0"></i>
                <span class="font-semibold text-gray-800">{{ $product['name'] }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- MAIN CONTENT (Left - 9 Cols) --}}
                <div class="lg:col-span-9 space-y-8">

                    {{-- Product Top Info & Gallery --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                            {{-- Gallery Section --}}
                            <div class="space-y-4">
                                <div
                                    class="relative aspect-square bg-gray-50 rounded-xl overflow-hidden border border-gray-100 group">
                                    {{-- Badges on Image --}}
                                    <div class="absolute top-4 left-4 z-10 flex flex-col gap-2">
                                        @if($product['special_offer'])
                                            <span
                                                class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-md shadow-md">HOT</span>
                                        @endif
                                    </div>
                                    <img :src="currentImage" :alt="product.name"
                                        class="w-full h-full object-contain p-4 transition-transform duration-500 hover:scale-110 cursor-zoom-in mix-blend-multiply">
                                </div>

                                {{-- Thumbnails --}}
                                <div class="grid grid-cols-5 gap-3">
                                    <div @click="currentImage = product.image_url"
                                        class="aspect-square rounded-lg border overflow-hidden cursor-pointer transition-all duration-200"
                                        :class="currentImage === product.image_url ? 'border-indigo-600 ring-1 ring-indigo-600 opacity-100' : 'border-gray-200 opacity-70 hover:opacity-100'">
                                        <img :src="product.image_url" class="w-full h-full object-contain p-1">
                                    </div>
                                    <template x-for="img in product.images" :key="img.id">
                                        <div @click="currentImage = img.image_url"
                                            class="aspect-square rounded-lg border overflow-hidden cursor-pointer transition-all duration-200"
                                            :class="currentImage === img.image_url ? 'border-indigo-600 ring-1 ring-indigo-600 opacity-100' : 'border-gray-200 opacity-70 hover:opacity-100'">
                                            <img :src="img.image_url" class="w-full h-full object-contain p-1">
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Info Section --}}
                            <div class="flex flex-col">
                                <div class="mb-4">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span
                                            class="text-indigo-600 font-bold text-[10px] uppercase tracking-wider bg-indigo-50 px-2 py-1 rounded border border-indigo-100">
                                            {{ $product['category']['name'] }}
                                        </span>
                                        <template x-if="product.status === 'active'">
                                            <span
                                                class="flex items-center gap-1 text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded border border-green-100">
                                                <i data-lucide="check-circle" class="w-3 h-3"></i> Đang kinh doanh
                                            </span>
                                        </template>
                                    </div>
                                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight mb-2">
                                        {{ $product['name'] }}
                                    </h1>
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <span>Mã SP: <span
                                                class="font-mono text-gray-700 font-medium">{{ $product['sku'] }}</span></span>
                                        @if($product['warranty'])
                                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                            <span>Bảo hành: <span
                                                    class="text-gray-700 font-medium">{{ $product['warranty'] }}</span></span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="flex items-end gap-3 mb-1">
                                        <span
                                            class="text-3xl font-bold text-indigo-600">{{ number_format($product['price'], 0, ',', '.') }}đ</span>
                                        @if($product['market_price'] > $product['price'])
                                            <span
                                                class="text-lg text-gray-400 line-through mb-1">{{ number_format($product['market_price'], 0, ',', '.') }}đ</span>
                                            <span class="text-xs font-bold text-red-600 bg-red-100 px-2 py-0.5 rounded mb-2">
                                                Tiết kiệm
                                                {{ number_format($product['market_price'] - $product['price'], 0, ',', '.') }}đ
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 italic">* Giá đã bao gồm VAT</p>
                                </div>

                                <div class="mb-6">
                                    <p class="text-gray-600 leading-relaxed text-sm">
                                        {{ $product['short_description'] ?? 'Mô tả ngắn đang cập nhật...' }}
                                    </p>
                                </div>

                                {{-- Action Area --}}
                                <div class="mt-auto space-y-6">
                                    {{-- Stock Status Indicator --}}
                                    <div class="flex items-center gap-2 text-sm">
                                        <div class="font-medium text-gray-700">Tình trạng:</div>
                                        @if($product['quantity'] > 10)
                                            <span class="text-green-600 font-bold flex items-center gap-1">
                                                <i data-lucide="check" class="w-4 h-4"></i> Còn hàng
                                            </span>
                                        @elseif($product['quantity'] > 0)
                                            <span class="text-orange-500 font-bold flex items-center gap-1">
                                                <i data-lucide="alert-circle" class="w-4 h-4"></i> Sắp hết (Còn
                                                {{ $product['quantity'] }})
                                            </span>
                                        @else
                                            <span class="text-red-500 font-bold flex items-center gap-1">
                                                <i data-lucide="x-circle" class="w-4 h-4"></i> Hết hàng
                                            </span>
                                        @endif
                                    </div>

                                    @if($product['quantity'] > 0)
                                        <div class="flex flex-col sm:flex-row gap-4">
                                            {{-- Qty Input --}}
                                            <div class="flex items-center border border-gray-300 rounded-xl bg-white w-fit">
                                                <button @click="if(quantity > 1) quantity--"
                                                    class="px-4 py-3 hover:bg-gray-50 text-gray-600 rounded-l-xl transition">
                                                    <i data-lucide="minus" class="w-4 h-4"></i>
                                                </button>
                                                <input type="number" x-model="quantity" readonly
                                                    class="w-12 text-center border-none focus:ring-0 font-bold text-gray-800 p-0 text-lg">
                                                <button @click="if(quantity < product.quantity) quantity++"
                                                    class="px-4 py-3 hover:bg-gray-50 text-gray-600 rounded-r-xl transition">
                                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                                </button>
                                            </div>

                                            {{-- Add to Cart Btn --}}
                                            <button @click="addToCart()"
                                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-6 rounded-xl font-bold shadow-lg shadow-indigo-200 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2 text-base">
                                                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                                                Thêm vào giỏ ngay
                                            </button>
                                        </div>
                                    @else
                                        <button disabled
                                            class="w-full bg-gray-200 text-gray-500 py-3 rounded-xl font-bold cursor-not-allowed">
                                            Sản phẩm đang tạm hết hàng
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Description Content --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <i data-lucide="file-text" class="w-5 h-5 text-indigo-600"></i> Mô tả chi tiết
                        </h3>
                        <div class="prose prose-indigo max-w-none text-gray-600">
                            {!! nl2br(e($product['description'])) !!}
                        </div>
                    </div>

                    {{-- Specs Table (Mobile/Tablet view mostly, or main view) --}}
                    @if(!empty($product['metadata']['specs']))
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <i data-lucide="cpu" class="w-5 h-5 text-indigo-600"></i> Thông số kỹ thuật
                            </h3>
                            <div class="overflow-hidden rounded-xl border border-gray-200">
                                <table class="w-full text-sm text-left">
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($product['metadata']['specs'] as $index => $spec)
                                            <tr class="{{ $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' }}">
                                                <td class="px-4 py-3 font-medium text-gray-600 w-1/3">{{ $spec['key'] }}</td>
                                                <td class="px-4 py-3 text-gray-800">{{ $spec['value'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- SIDEBAR (Right - 3 Cols) --}}
                <div class="lg:col-span-3 space-y-6">

                    {{-- Policy Box --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 ">
                        <h4 class="font-bold text-gray-900 mb-4 text-base">Yên tâm mua hàng</h4>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center shrink-0 text-indigo-600">
                                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                                </div>
                                <div class="text-sm">
                                    <span class="block font-bold text-gray-800">Hàng chính hãng</span>
                                    <span class="text-gray-500 text-xs">Cam kết 100% chất lượng</span>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center shrink-0 text-indigo-600">
                                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                </div>
                                <div class="text-sm">
                                    <span class="block font-bold text-gray-800">Đổi trả dễ dàng</span>
                                    <span class="text-gray-500 text-xs">Trong vòng 15 ngày đầu</span>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center shrink-0 text-indigo-600">
                                    <i data-lucide="truck" class="w-4 h-4"></i>
                                </div>
                                <div class="text-sm">
                                    <span class="block font-bold text-gray-800">Miễn phí vận chuyển</span>
                                    <span class="text-gray-500 text-xs">Cho đơn hàng > 2 triệu</span>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center shrink-0 text-indigo-600">
                                    <i data-lucide="headphones" class="w-4 h-4"></i>
                                </div>
                                <div class="text-sm">
                                    <span class="block font-bold text-gray-800">Hỗ trợ 24/7</span>
                                    <span class="text-gray-500 text-xs">Hotline: 1900 1234</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    {{-- Related Products Sidebar Style --}}
                    @if($related->isNotEmpty())
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sticky top-24">

                            <h4 class="font-bold text-gray-900 mb-4">Sản phẩm tương tự</h4>
                            <div class="space-y-4">
                                @foreach($related->take(4) as $item)
                                    <a href="{{ route('product.show', $item->slug) }}" class="flex gap-3 group">
                                        <div
                                            class="w-16 h-16 rounded-lg border border-gray-100 bg-gray-50 shrink-0 overflow-hidden">
                                            <img src="{{ $item->image_url }}"
                                                class="w-full h-full object-contain p-1 group-hover:scale-110 transition">
                                        </div>
                                        <div class="flex flex-col justify-center">
                                            <h5
                                                class="text-xs font-medium text-gray-800 line-clamp-2 mb-1 group-hover:text-indigo-600 transition">
                                                {{ $item->name }}
                                            </h5>
                                            <span
                                                class="text-sm font-bold text-indigo-600">{{ number_format($item->price, 0, ',', '.') }}đ</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function productDetail(productData) {
            return {
                product: productData,
                currentImage: productData.image_url,
                quantity: 1,
                isAdding: false,

                async addToCart() {
                    if (this.isAdding || this.quantity < 1) return;
                    this.isAdding = true;

                    try {
                        const res = await axios.post('{{ route("cart.add") }}', {
                            product_id: this.product.id,
                            quantity: this.quantity
                        });

                        if (res.data.success) {
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { message: 'Đã thêm sản phẩm vào giỏ hàng thành công!', type: 'success' }
                            }));

                            window.dispatchEvent(new CustomEvent('cart-updated', {
                                detail: { count: res.data.cart_count }
                            }));
                        }
                    } catch (e) {
                        let msg = e.response?.data?.message || 'Không thể thêm vào giỏ';
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { message: 'Lỗi: ' + msg, type: 'error' }
                        }));
                    } finally {
                        this.isAdding = false;
                    }
                }
            }
        }
    </script>
@endsection
