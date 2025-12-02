@extends('layouts.app')

@section('title', 'Tìm kiếm: ' . $keyword)

@section('content')
    <div class="bg-gray-50 min-h-screen py-8 font-sans" x-data="searchPage()">
        <div class="container mx-auto px-4">

            {{-- 1. Breadcrumb & Header --}}
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6 overflow-x-auto whitespace-nowrap pb-2">
                <a href="{{ route('home') }}" class="hover:text-indigo-600 transition-colors">Trang chủ</a>
                <i data-lucide="chevron-right" class="w-4 h-4 shrink-0"></i>
                <span class="text-gray-900 font-medium">Tìm kiếm</span>
            </nav>

            {{-- 2. Search Summary & Controls --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                        Kết quả cho: "<span class="text-indigo-600">{{ $keyword }}</span>"
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Tìm thấy <strong>{{ $products->total() }}</strong> sản phẩm phù
                        hợp</p>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Mobile Filter Button --}}
                    <button @click="showMobileFilter = true"
                        class="md:hidden flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <i data-lucide="filter" class="w-4 h-4"></i> Bộ lọc
                    </button>

                    {{-- Sort Dropdown --}}
                    <div class="relative min-w-[180px]">
                        <select x-model="sort" @change="applySort()"
                            class="w-full appearance-none bg-white border border-gray-200 text-gray-700 py-2 pl-4 pr-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm cursor-pointer shadow-sm">
                            <option value="created_at">Mới nhất</option>
                            <option value="price_asc">Giá: Thấp đến Cao</option>
                            <option value="price_desc">Giá: Cao đến Thấp</option>
                            <option value="name_asc">Tên: A-Z</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-8 relative">

                {{-- 3. SIDEBAR FILTER (Desktop & Mobile Drawer) --}}
                {{-- Mobile Overlay --}}
                <div x-show="showMobileFilter" class="fixed inset-0 bg-black/50 z-40 md:hidden" x-transition.opacity
                    @click="showMobileFilter = false"></div>

                {{-- Sidebar Content --}}
                <aside
                    class="fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-2xl transform transition-transform md:translate-x-0 md:static md:w-1/4 md:shadow-none md:bg-transparent md:z-0 h-full overflow-y-auto md:h-auto"
                    :class="showMobileFilter ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

                    <div class="p-5 md:p-0">
                        {{-- <div class="flex items-center justify-between mb-6 md:hidden">
                            <span class="font-bold text-lg text-gray-900">Bộ lọc tìm kiếm</span>
                            <button @click="showMobileFilter = false" class="p-1 hover:bg-gray-100 rounded-full">
                                <i data-lucide="x" class="w-6 h-6"></i>
                            </button>
                        </div> --}}

                        {{-- Reuse your existing filters partial or build a wrapper here --}}
                        <div class="bg-white rounded-2xl md:shadow-sm md:border border-gray-100 md:p-6 space-y-6">
                            @include('partials.filters')
                        </div>
                    </div>
                </aside>

                {{-- 4. MAIN PRODUCT GRID --}}
                <div class="w-full md:w-3/4">
                    @if($products->count() > 0)
                        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                            @foreach($products as $product)
                                {{-- Product Card --}}
                                <div
                                    class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col relative">
                                    {{-- Badges --}}
                                    <div class="absolute top-3 left-3 z-10 flex flex-col gap-1.5">
                                        @if($product->price < ($product->market_price ?? 0))
                                            <span
                                                class="px-2 py-1 bg-red-500 text-white text-[10px] font-bold uppercase tracking-wide rounded shadow-sm">
                                                -{{ round((($product->market_price - $product->price) / $product->market_price) * 100) }}%
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Image --}}
                                    <a href="{{ route('product.show', $product->slug) }}"
                                        class="block relative pt-[100%] bg-gray-50 overflow-hidden">
                                        @php
                                            $imgUrl = $product->product_images->first()?->image_url ?? asset('images/no-image.jpg');
                                        @endphp
                                        <img src="{{ $imgUrl }}" alt="{{ $product->name }}"
                                            class="absolute top-0 left-0 w-full h-full object-contain p-4 transition-transform duration-500 group-hover:scale-110 mix-blend-multiply">
                                    </a>

                                    {{-- Info --}}
                                    <div class="p-4 flex flex-col flex-grow">
                                        {{-- Category Name (Optional) --}}
                                        <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider mb-1">
                                            {{ $product->category->name ?? 'Sản phẩm' }}
                                        </span>

                                        <h3
                                            class="text-sm font-semibold text-gray-800 line-clamp-2 mb-2 group-hover:text-indigo-600 transition-colors min-h-[40px]">
                                            <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                        </h3>

                                        <div class="mt-auto pt-2 border-t border-gray-50">
                                            <div class="flex flex-wrap items-baseline gap-2 mb-3">
                                                <span class="text-base md:text-lg font-bold text-indigo-700">
                                                    {{ number_format($product->price, 0, ',', '.') }}đ
                                                </span>
                                                @if($product->price < ($product->market_price ?? 0))
                                                    <span class="text-xs text-gray-400 line-through">
                                                        {{ number_format($product->market_price, 0, ',', '.') }}đ
                                                    </span>
                                                @endif
                                            </div>

                                            <button @click="addToCart({{ $product->id }})"
                                                class="w-full py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white font-semibold text-sm rounded-lg transition-all flex items-center justify-center gap-2 group/btn">
                                                <i data-lucide="shopping-cart"
                                                    class="w-4 h-4 transition-transform group-hover/btn:scale-110"></i>
                                                <span class="hidden sm:inline">Thêm vào giỏ</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-10">
                            {{ $products->appends(request()->query())->links() }}
                        </div>

                    @else
                        {{-- Empty State --}}
                        <div class="bg-white rounded-2xl border border-gray-100 p-10 text-center shadow-sm">
                            <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="search-x" class="w-10 h-10 text-indigo-400"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-2">Không tìm thấy kết quả nào</h2>
                            <p class="text-gray-500 max-w-md mx-auto mb-6">
                                Rất tiếc, chúng tôi không tìm thấy sản phẩm nào khớp với từ khóa
                                "<strong>{{ $keyword }}</strong>". Hãy thử kiểm tra lại chính tả hoặc sử dụng từ khóa chung
                                chung hơn.
                            </p>
                            <a href="{{ route('home') }}"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-all shadow-lg shadow-indigo-200">
                                <i data-lucide="arrow-left" class="w-4 h-4"></i> Quay về trang chủ
                            </a>
                        </div>

                        {{-- Suggested Products (Logic from Controller) [cite: 34, 108] --}}
                        @if(isset($suggestedProducts) && $suggestedProducts->count() > 0)
                            <div class="mt-12">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-1 h-6 bg-indigo-600 rounded-full"></div>
                                    <h3 class="text-xl font-bold text-gray-900">Có thể bạn quan tâm</h3>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @foreach($suggestedProducts as $product)
                                        <div
                                            class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg transition-all">
                                            <a href="{{ route('product.show', $product->slug) }}" class="block relative pt-[100%]">
                                                <img src="{{ $product->product_images->first()?->image_url ?? asset('images/no-image.jpg') }}"
                                                    class="absolute top-0 left-0 w-full h-full object-contain p-3 group-hover:scale-105 transition-transform">
                                            </a>
                                            <div class="p-3">
                                                <h4 class="text-xs font-semibold text-gray-800 line-clamp-2 mb-1">
                                                    <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                                </h4>
                                                <div class="font-bold text-indigo-600 text-sm">
                                                    {{ number_format($product->price, 0, ',', '.') }}đ
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function searchPage() {
            return {
                showMobileFilter: false,
                sort: '{{ request('sort', 'created_at') }}', // Lấy giá trị sort từ URL hiện tại [cite: 8, 30]

                applySort() {
                    let url = new URL(window.location.href);
                    url.searchParams.set('sort', this.sort);
                    // Reset về trang 1 khi sort lại
                    url.searchParams.delete('page');
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
