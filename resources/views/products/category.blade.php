@extends('layouts.app')

@section('title', $category->name)

@section('content')
    <div class="bg-gray-50 min-h-screen py-8" x-data="categoryPage()">
        <div class="container mx-auto px-4">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-8">
                <a href="{{ route('home') }}" class="hover:text-indigo-600 transition-colors">Trang chủ</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span class="font-semibold text-gray-900">{{ $category->name }}</span>
            </nav>

            <div class="flex flex-col lg:flex-row gap-8">

                {{-- Sidebar Filters --}}
                <aside class="w-full lg:w-1/4 shrink-0">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-bold text-lg text-gray-900">Bộ lọc tìm kiếm</h3>
                            <i data-lucide="filter" class="w-5 h-5 text-indigo-600"></i>
                        </div>

                        {{-- Sort --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sắp xếp theo</label>
                            <div class="relative">
                                <select x-model="sort" @change="applyFilters()"
                                    class="w-full appearance-none bg-gray-50 border border-gray-200 text-gray-700 py-2.5 px-4 pr-8 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                    <option value="newest">Mới nhất</option>
                                    <option value="price_asc">Giá: Thấp đến Cao</option>
                                    <option value="price_desc">Giá: Cao đến Thấp</option>
                                    <option value="name_asc">Tên: A-Z</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Price Range --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Khoảng giá (VNĐ)</label>
                            <div class="flex items-center gap-2 mb-3">
                                <input type="number" x-model="priceMin" placeholder="0"
                                    class="w-full bg-gray-50 border-gray-200 rounded-lg text-sm p-2.5 focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="text-gray-400">-</span>
                                <input type="number" x-model="priceMax" placeholder="Max"
                                    class="w-full bg-gray-50 border-gray-200 rounded-lg text-sm p-2.5 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <button @click="applyFilters()"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-xl text-sm transition-all shadow-lg shadow-indigo-200">
                                Áp dụng
                            </button>
                        </div>
                    </div>
                </aside>

                {{-- Product Grid --}}
                <div class="flex-1">
                    <div class="flex justify-between items-end mb-6">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-1">{{ $category->name }}</h1>
                            <p class="text-sm text-gray-500">Hiển thị {{ $products->count() }} / {{ $products->total() }} sản phẩm</p>
                        </div>
                    </div>

                    @if($products->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($products as $product)
                                <div class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col relative">

                                    {{-- Badges --}}
                                    <div class="absolute top-3 left-3 z-10 flex flex-col gap-2">
                                        @if($product->special_offer)
                                            <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wide shadow-sm">Hot Deal</span>
                                        @endif
                                        @if($product->market_price > $product->price)
                                            <span class="bg-indigo-600 text-white text-[10px] font-bold px-2 py-1 rounded-md shadow-sm">
                                                -{{ round((($product->market_price - $product->price) / $product->market_price) * 100) }}%
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Image --}}
                                    <a href="{{ route('product.show', $product->slug) }}" class="block relative pt-[100%] bg-white overflow-hidden">
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                            class="absolute top-0 left-0 w-full h-full object-contain p-6 transition-transform duration-500 group-hover:scale-110">

                                        {{-- Quick Status Overlay --}}
                                        @if($product->quantity <= 0)
                                            <div class="absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center">
                                                <span class="bg-gray-800 text-white text-xs font-bold px-3 py-1.5 rounded-full">Hết hàng</span>
                                            </div>
                                        @endif
                                    </a>

                                    {{-- Info --}}
                                    <div class="p-5 flex flex-col flex-grow">
                                        {{-- Stock Status & Warranty --}}
                                        <div class="flex items-center gap-2 mb-2 text-[11px] font-medium">
                                            @if($product->quantity > 0)
                                                <span class="text-green-600 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Còn hàng
                                                </span>
                                            @else
                                                <span class="text-gray-500 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Hết hàng
                                                </span>
                                            @endif

                                            @if($product->warranty)
                                                <span class="text-gray-300">|</span>
                                                <span class="text-gray-500 truncate max-w-[100px]" title="Bảo hành {{ $product->warranty }}">
                                                    BH: {{ $product->warranty }}
                                                </span>
                                            @endif
                                        </div>

                                        <h3 class="text-base font-semibold text-gray-800 line-clamp-2 mb-1 group-hover:text-indigo-600 transition-colors">
                                            <a href="{{ route('product.show', $product->slug) }}">
                                                {{ $product->name }}
                                            </a>
                                        </h3>

                                        <div class="mt-auto pt-3">
                                            <div class="flex flex-wrap items-baseline gap-2 mb-4">
                                                <span class="text-lg font-bold text-indigo-600">
                                                    {{ number_format($product->price, 0, ',', '.') }}đ
                                                </span>
                                                @if($product->market_price > $product->price)
                                                    <span class="text-xs text-gray-400 line-through font-medium">
                                                        {{ number_format($product->market_price, 0, ',', '.') }}đ
                                                    </span>
                                                @endif
                                            </div>

                                            <button @click="addToCart({{ $product->id }})"
                                                class="w-full py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2
                                                {{ $product->quantity > 0
                                                    ? 'bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white hover:shadow-lg hover:shadow-indigo-200'
                                                    : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}"
                                                {{ $product->quantity <= 0 ? 'disabled' : '' }}>
                                                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                                                {{ $product->quantity > 0 ? 'Thêm vào giỏ' : 'Tạm hết hàng' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-10">
                            {{ $products->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm">
                            <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="search-x" class="w-8 h-8 text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Không tìm thấy sản phẩm</h3>
                            <p class="text-gray-500 mt-2">Hãy thử thay đổi bộ lọc hoặc tìm kiếm từ khóa khác.</p>
                            <button @click="resetFilters()" class="mt-4 text-indigo-600 font-medium hover:underline">Xóa bộ lọc</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function categoryPage() {
            return {
                sort: '{{ request('sort', 'newest') }}',
                priceMin: '{{ request('price_min') }}',
                priceMax: '{{ request('price_max') }}',

                applyFilters() {
                    let url = new URL(window.location.href);
                    if (this.sort) url.searchParams.set('sort', this.sort);
                    if (this.priceMin) url.searchParams.set('price_min', this.priceMin);
                    if (this.priceMax) url.searchParams.set('price_max', this.priceMax);
                    // Reset page to 1 when filtering
                    url.searchParams.delete('page');
                    window.location.href = url.toString();
                },

                resetFilters() {
                    let url = new URL(window.location.href);
                    url.searchParams.delete('sort');
                    url.searchParams.delete('price_min');
                    url.searchParams.delete('price_max');
                    window.location.href = url.toString();
                },

                async addToCart(productId) {
                    try {
                        const res = await axios.post('{{ route("cart.add") }}', {
                            product_id: productId,
                            quantity: 1
                        });
                        if (res.data.success) {
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { message: 'Đã thêm vào giỏ hàng!', type: 'success' }
                            }));
                            window.dispatchEvent(new CustomEvent('cart-updated', {
                                detail: { count: res.data.cart_count }
                            }));
                        }
                    } catch (e) {
                        let msg = e.response?.data?.message || 'Có lỗi xảy ra';
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { message: msg, type: 'error' }
                        }));
                    }
                }
            }
        }
    </script>
@endsection
