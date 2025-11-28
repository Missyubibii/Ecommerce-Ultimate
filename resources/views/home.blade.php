@extends('layouts.app')

@section('content')
    <div x-data="homePageHandler()" x-init="initProductData()">

        <div class="pb-12">

            {{-- PHẦN 1: BANNER (Render bằng Blade) --}}
            <div class="bg-white py-6 shadow-sm">
                <div class="container mx-auto px-4">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                        <div class="lg:col-span-8 group relative rounded-xl overflow-hidden shadow-sm">
                            <div class="swiper w-full h-[300px] md:h-[400px] lg:h-[460px]" id="main-banner-swiper">
                                <div class="swiper-wrapper">
                                    @foreach($mainBanners as $banner)
                                        <div class="swiper-slide">
                                            <a href="{{ $banner->url ?? '#' }}" class="block w-full h-full relative overflow-hidden">
                                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                                                    class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                                                @if($banner->title)
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent flex items-end">
                                                    <div class="px-6 py-8 w-full">
                                                        <h2 class="text-white text-2xl md:text-4xl font-bold drop-shadow-md mb-2">{{ $banner->title }}</h2>
                                                        <span class="inline-block px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded hover:bg-indigo-700 transition">Xem ngay</span>
                                                    </div>
                                                </div>
                                                @endif
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination"></div>
                                <div class="swiper-button-prev !hidden md:!flex !text-white !bg-black/30 !w-10 !h-10 !rounded-full backdrop-blur-sm hover:!bg-indigo-600 transition"></div>
                                <div class="swiper-button-next !hidden md:!flex !text-white !bg-black/30 !w-10 !h-10 !rounded-full backdrop-blur-sm hover:!bg-indigo-600 transition"></div>
                            </div>
                        </div>

                        <div class="hidden lg:flex lg:col-span-4 flex-col gap-4 h-[460px]">
                            @if(isset($ads['pos_1']))
                                <a href="{{ $ads['pos_1']->url ?? '#' }}" class="flex-1 relative rounded-xl overflow-hidden shadow-sm group">
                                    <img src="{{ $ads['pos_1']->image_url }}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                                </a>
                            @else
                                <div class="flex-1 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400">Vị trí Quảng cáo 1</div>
                            @endif

                            @if(isset($ads['pos_2']))
                                <a href="{{ $ads['pos_2']->url ?? '#' }}" class="flex-1 relative rounded-xl overflow-hidden shadow-sm group">
                                    <img src="{{ $ads['pos_2']->image_url }}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                                </a>
                            @else
                                <div class="flex-1 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400">Vị trí Quảng cáo 2</div>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        @foreach(['pos_3', 'pos_4', 'pos_5'] as $pos)
                            @if(isset($ads[$pos]))
                                <a href="{{ $ads[$pos]->url ?? '#' }}" class="relative h-32 md:h-40 rounded-xl overflow-hidden shadow-sm group">
                                    <img src="{{ $ads[$pos]->image_url }}" alt="{{ $ads[$pos]->title }}"
                                        class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                                </a>
                            @else
                                <div class="relative h-32 md:h-40 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 text-sm">
                                    Vị trí {{ substr($pos, -1) }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- PHẦN 2: SẢN PHẨM --}}
            <div class="py-10 bg-gray-50">
                <div class="container mx-auto px-4 space-y-16">
                    <div x-show="isLoading" class="flex justify-center items-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                    </div>

                    <template x-for="section in sections" :key="section.id">
                        <div class="category-block bg-white rounded-lg p-6 shadow-lg transition-shadow duration-300" x-show="section.products && section.products.length > 0">
                            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 border-b border-gray-200 pb-4">
                                <a :href="section.url"
                                    class="text-2xl md:text-3xl font-bold text-gray-800 hover:text-indigo-600 transition-colors duration-300"
                                    x-text="section.name">
                                </a>
                                <div class="mt-4 md:mt-0">
                                    <a :href="section.url" class="w-full flex items-center justify-center gap-2 border-2 border-indigo-600 text-indigo-600 font-semibold py-2 px-4 rounded-lg hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out text-sm group/btn">
                                        Xem tất cả
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <div class="relative group/slider pt-4">
                                <div class="swiper product-swiper px-1 py-4" :id="'swiper-cat-' + section.id" style="min-height:360px;">
                                    <div class="swiper-wrapper">
                                        <template x-for="product in section.products" :key="product.id">
                                            <div class="swiper-slide h-auto p-1">
                                                <div class="group h-full flex flex-col bg-white rounded-lg border border-gray-200 hover:shadow-lg transition-all duration-300 relative overflow-hidden">

                                                    {{-- Logic giảm giá dùng market_price --}}
                                                    <template x-if="product.market_price > product.price">
                                                        <div class="absolute top-0 right-0 bg-indigo-600 text-white text-xs font-bold px-2 py-1 rounded-bl-lg z-10 shadow-sm"
                                                            x-text="'-' + Math.round(((product.market_price - product.price) / product.market_price) * 100) + '%'">
                                                        </div>
                                                    </template>

                                                    <div class="p-4 relative">
                                                        <a :href="product.detail_url" class="block overflow-hidden aspect-[1/1]">
                                                            <img :src="product.image_url" :alt="product.name"
                                                                class="w-full h-full object-contain transform group-hover:scale-110 transition-transform duration-500">
                                                        </a>
                                                    </div>

                                                    <div class="p-4 border-t border-gray-100 flex flex-col flex-grow">
                                                        <a :href="product.detail_url"
                                                            class="text-sm font-semibold text-gray-800 hover:text-indigo-600 mb-2 line-clamp-2 min-h-[40px] transition-colors"
                                                            :title="product.name"
                                                            x-text="product.name">
                                                        </a>

                                                        <div class="mb-3">
                                                            <div class="flex flex-wrap items-center gap-2 text-[10px] sm:text-xs">
                                                                <template x-if="product.quantity > 0">
                                                                    <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded-full font-medium whitespace-nowrap">Còn hàng</span>
                                                                </template>
                                                                <template x-if="product.quantity <= 0">
                                                                    <span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded-full font-medium whitespace-nowrap">Hết hàng</span>
                                                                </template>

                                                                <template x-if="product.warranty">
                                                                    <span class="bg-cyan-50 text-cyan-700 px-2 py-0.5 rounded-full font-medium whitespace-nowrap truncate max-w-[120px]"
                                                                            x-text="'Bảo hành: ' + product.warranty"></span>
                                                                </template>
                                                            </div>
                                                        </div>

                                                        <div class="mt-auto">
                                                            <template x-if="product.special_offer">
                                                                <div class="p-2 rounded bg-red-50 border border-red-100 text-red-700 text-xs mb-3 line-clamp-2"
                                                                        x-text="product.special_offer">
                                                                </div>
                                                            </template>

                                                            <div class="flex flex-wrap items-baseline gap-2 mb-4">
                                                                <span class="text-lg font-bold text-indigo-600" x-text="formatMoney(product.price)"></span>
                                                                {{-- Giá cũ gạch ngang dùng market_price --}}
                                                                <template x-if="product.market_price > product.price">
                                                                    <span class="text-xs text-gray-400 line-through" x-text="formatMoney(product.market_price)"></span>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="p-4 pt-0">
                                                        <button @click="addToCart(product.id)"
                                                            class="w-full flex items-center justify-center gap-2 border-2 border-indigo-600 text-indigo-600 font-semibold py-2 px-4 rounded-lg hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out text-sm group/btn">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover/btn:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                            </svg>
                                                            Thêm vào giỏ
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                {{-- NÚT PREV (Bên trái) --}}
                                <div :class="'prev-cat-' + section.id"
                                    class="absolute top-1/2 -translate-y-1/2 left-0 md:-left-4 z-20 w-10 h-10 bg-white rounded-full shadow-md border border-gray-100 items-center justify-center text-gray-600 cursor-pointer opacity-0 group-hover/slider:opacity-100 transition-all duration-300 hover:bg-indigo-600 hover:text-white hover:scale-110 !hidden md:!flex">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                        <path d="M15 18l-6-6 6-6"/>
                                    </svg>
                                </div>

                                {{-- NÚT NEXT (Bên phải) --}}
                                <div :class="'next-cat-' + section.id"
                                    class="absolute top-1/2 -translate-y-1/2 right-0 md:-right-4 z-20 w-10 h-10 bg-white rounded-full shadow-md border border-gray-100 items-center justify-center text-gray-600 cursor-pointer opacity-0 group-hover/slider:opacity-100 transition-all duration-300 hover:bg-indigo-600 hover:text-white hover:scale-110 !hidden md:!flex">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                        <path d="M9 18l6-6-6-6"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('main-banner-swiper')) {
                new Swiper('#main-banner-swiper', {
                    loop: true,
                    speed: 800,
                    autoplay: { delay: 5000, disableOnInteraction: false },
                    pagination: { el: '.swiper-pagination', clickable: true },
                    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                });
            }
        });

        function homePageHandler() {
            return {
                isLoading: true,
                sections: [],

                async initProductData() {
                    try {
                        const response = await axios.get('{{ route("home") }}?api=1', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });

                        if (response.data.success) {
                            this.sections = response.data.data.sections;
                        }
                    } catch (error) {
                        console.error('Lỗi load sản phẩm:', error);
                    } finally {
                        this.isLoading = false;
                        this.$nextTick(() => {
                            this.initProductSwipers();
                        });
                    }
                },

                initProductSwipers() {
                    this.sections.forEach(section => {
                        const el = document.getElementById('swiper-cat-' + section.id);
                        if (el && section.products.length > 0) {
                            new Swiper(el, {
                                slidesPerView: 2,
                                spaceBetween: 12,
                                observer: true,
                                observeParents: true,
                                navigation: {
                                    nextEl: '.next-cat-' + section.id,
                                    prevEl: '.prev-cat-' + section.id,
                                },
                                breakpoints: {
                                    640: { slidesPerView: 2, spaceBetween: 16 },
                                    768: { slidesPerView: 3, spaceBetween: 20 },
                                    1024: { slidesPerView: 4, spaceBetween: 24 },
                                    1280: { slidesPerView: 5, spaceBetween: 24 },
                                }
                            });
                        }
                    });
                },

                formatMoney(amount) {
                    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
                },

                async addToCart(productId) {
                    try {
                        const res = await axios.post('{{ route("cart.add") }}', { product_id: productId, quantity: 1 });
                        if(res.data.success) {
                            window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Đã thêm vào giỏ hàng!', type: 'success' } }));
                            window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: res.data.cart_count } }));
                        }
                    } catch (e) {
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Lỗi thêm giỏ hàng', type: 'error' } }));
                    }
                }
            }
        }
    </script>
@endsection
