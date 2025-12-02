@extends('layouts.app')

@section('content')
    <div x-data="homePageHandler()" x-init="initProductData()" class="bg-gray-50 min-h-screen font-sans">

        <div class="pb-20 space-y-10">

            {{-- PHẦN 1: HERO SECTION (BANNER + ADS) --}}
            <div class="pt-6 pb-2">
                <div class="container mx-auto px-4">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                        {{-- Main Slider (8 cols) --}}
                        <div class="lg:col-span-8 group relative rounded-2xl overflow-hidden shadow-lg border border-gray-100 bg-white">
                            <div class="swiper w-full h-[320px] md:h-[420px] lg:h-[480px]" id="main-banner-swiper">
                                <div class="swiper-wrapper">
                                    @foreach($mainBanners as $banner)
                                        <div class="swiper-slide relative">
                                            <a href="{{ $banner->url ?? '#' }}" class="block w-full h-full relative overflow-hidden group/slide">
                                                <div class="absolute inset-0 bg-slate-900/10 group-hover/slide:bg-transparent transition-colors z-10"></div>
                                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                                                    class="w-full h-full object-cover transform group-hover/slide:scale-105 transition-transform duration-1000 ease-out">

                                                @if($banner->title)
                                                <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/80 via-black/40 to-transparent p-6 md:p-10 z-20">
                                                    <div class="transform translate-y-4 group-hover/slide:translate-y-0 transition-transform duration-500">
                                                        <h2 class="text-white text-2xl md:text-4xl font-extrabold tracking-tight drop-shadow-lg mb-3 leading-tight">{{ $banner->title }}</h2>
                                                        <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600/90 backdrop-blur-sm hover:bg-indigo-600 text-white text-sm font-semibold rounded-full shadow-lg transition-all duration-300 transform hover:scale-105">
                                                            Khám phá ngay
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                        </span>
                                                    </div>
                                                </div>
                                                @endif
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                                {{-- Pagination & Nav --}}
                                <div class="swiper-pagination !bottom-6"></div>
                                <div class="swiper-button-prev !w-12 !h-12 !rounded-full !bg-white/10 !backdrop-blur-md !text-white hover:!bg-indigo-600 border border-white/20 transition-all !hidden md:!flex after:!text-lg"></div>
                                <div class="swiper-button-next !w-12 !h-12 !rounded-full !bg-white/10 !backdrop-blur-md !text-white hover:!bg-indigo-600 border border-white/20 transition-all !hidden md:!flex after:!text-lg"></div>
                            </div>
                        </div>

                        {{-- Side Ads (4 cols) --}}
                        <div class="hidden lg:flex lg:col-span-4 flex-col gap-6 h-[480px]">
                            @if(isset($ads['pos_1']))
                                <a href="{{ $ads['pos_1']->url ?? '#' }}" class="flex-1 relative rounded-2xl overflow-hidden shadow-md group border border-gray-100">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors z-10"></div>
                                    <img src="{{ $ads['pos_1']->image_url }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                                </a>
                            @else
                                <div class="flex-1 bg-indigo-50/50 border-2 border-dashed border-indigo-200 rounded-2xl flex flex-col items-center justify-center text-indigo-400 gap-2">
                                    <span class="text-4xl font-light">+</span>
                                    <span class="text-sm font-medium">Quảng cáo 1</span>
                                </div>
                            @endif

                            @if(isset($ads['pos_2']))
                                <a href="{{ $ads['pos_2']->url ?? '#' }}" class="flex-1 relative rounded-2xl overflow-hidden shadow-md group border border-gray-100">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors z-10"></div>
                                    <img src="{{ $ads['pos_2']->image_url }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                                </a>
                            @else
                                <div class="flex-1 bg-indigo-50/50 border-2 border-dashed border-indigo-200 rounded-2xl flex flex-col items-center justify-center text-indigo-400 gap-2">
                                    <span class="text-4xl font-light">+</span>
                                    <span class="text-sm font-medium">Quảng cáo 2</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Bottom Ads Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        @foreach(['pos_3', 'pos_4', 'pos_5'] as $pos)
                            @if(isset($ads[$pos]))
                                <a href="{{ $ads[$pos]->url ?? '#' }}" class="relative h-36 md:h-44 rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 group">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-60 group-hover:opacity-40 transition-opacity z-10"></div>
                                    <img src="{{ $ads[$pos]->image_url }}" alt="{{ $ads[$pos]->title }}"
                                        class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                                    <div class="absolute bottom-4 left-4 z-20">
                                        <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md border border-white/30 rounded-lg text-white text-xs font-bold uppercase tracking-wider group-hover:bg-indigo-600 group-hover:border-indigo-600 transition-colors">
                                            Khám phá
                                        </span>
                                    </div>
                                </a>
                            @else
                                <div class="relative h-36 md:h-44 bg-gray-100 rounded-2xl flex items-center justify-center text-gray-400 text-sm border border-gray-200">
                                    Space {{ substr($pos, -1) }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- PHẦN 2: DANH SÁCH SẢN PHẨM --}}
            <div class="container mx-auto px-4 space-y-12">
                {{-- Loading Spinner --}}
                <div x-show="isLoading" class="flex flex-col items-center justify-center py-20" x-transition>
                    <div class="relative w-16 h-16">
                        <div class="absolute top-0 left-0 w-full h-full border-4 border-gray-200 rounded-full"></div>
                        <div class="absolute top-0 left-0 w-full h-full border-4 border-indigo-600 rounded-full border-t-transparent animate-spin"></div>
                    </div>
                    <span class="mt-4 text-gray-500 font-medium animate-pulse">Đang tải sản phẩm...</span>
                </div>

                <template x-for="section in sections" :key="section.id">
                    <div class="section-container" x-show="section.products && section.products.length > 0">

                        {{-- Section Header --}}
                        <div class="flex items-center justify-between mb-6 px-2">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-8 bg-indigo-600 rounded-full"></div>
                                <h3 class="text-2xl md:text-3xl font-bold text-gray-800 tracking-tight">
                                    <a :href="section.url" class="hover:text-indigo-600 transition-colors" x-text="section.name"></a>
                                </h3>
                            </div>
                            <a :href="section.url" class="group flex items-center gap-1 text-sm font-semibold text-indigo-600 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-full transition-all">
                                Xem tất cả
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        {{-- Product Slider Container --}}
                        <div class="relative group/slider">
                            <div class="swiper product-swiper !pb-10 !px-1" :id="'swiper-cat-' + section.id">
                                <div class="swiper-wrapper">
                                    <template x-for="product in section.products" :key="product.id">
                                        <div class="swiper-slide h-auto">

                                            {{-- Product Card --}}
                                            <div class="group h-full bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col relative overflow-hidden">

                                                {{-- Badges --}}
                                                <div class="absolute top-3 left-3 z-10 flex flex-col gap-1.5">
                                                    <template x-if="product.market_price > product.price">
                                                        <span class="px-2.5 py-1 bg-red-500 text-white text-[10px] font-bold uppercase tracking-wide rounded-md shadow-sm"
                                                            x-text="'-' + Math.round(((product.market_price - product.price) / product.market_price) * 100) + '%'">
                                                        </span>
                                                    </template>
                                                    <template x-if="product.special_offer">
                                                        <span class="px-2.5 py-1 bg-indigo-500 text-white text-[10px] font-bold uppercase tracking-wide rounded-md shadow-sm">Hot</span>
                                                    </template>
                                                </div>

                                                {{-- Image Area --}}
                                                <div class="relative p-4 aspect-[1/1] overflow-hidden bg-gray-50/50">
                                                    <a :href="product.detail_url" class="block w-full h-full">
                                                        <img :src="product.image_url" :alt="product.name"
                                                            class="w-full h-full object-contain mix-blend-multiply transform group-hover:scale-110 transition-transform duration-500 ease-in-out">
                                                    </a>
                                                    {{-- Quick Action Overlay (Optional visual) --}}
                                                    <div class="absolute bottom-0 left-0 w-full p-2 translate-y-full group-hover:translate-y-0 transition-transform duration-300 z-10">
                                                        <button @click="addToCart(product.id)"
                                                            class="w-full bg-white/90 backdrop-blur text-indigo-700 font-bold py-2 rounded-xl shadow-lg hover:bg-indigo-600 hover:text-white transition-colors border border-indigo-100 text-sm flex items-center justify-center gap-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                                            Thêm nhanh
                                                        </button>
                                                    </div>
                                                </div>

                                                {{-- Content Area --}}
                                                <div class="p-4 flex flex-col flex-grow">
                                                    {{-- Meta tags --}}
                                                    <div class="flex items-center gap-2 mb-2 text-[10px] font-medium text-gray-500">
                                                        <template x-if="product.quantity > 0">
                                                            <span class="text-green-600 flex items-center gap-1">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Còn hàng
                                                            </span>
                                                        </template>
                                                        <template x-if="product.quantity <= 0">
                                                            <span class="text-gray-400">Hết hàng</span>
                                                        </template>
                                                    </div>

                                                    {{-- Name --}}
                                                    <a :href="product.detail_url"
                                                        class="text-gray-800 font-semibold text-sm leading-snug hover:text-indigo-600 line-clamp-2 mb-2 transition-colors min-h-[40px]"
                                                        :title="product.name"
                                                        x-text="product.name">
                                                    </a>

                                                    {{-- Warranty Info --}}
                                                    <template x-if="product.warranty">
                                                        <div class="text-xs text-gray-500 mb-3 flex items-center gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                                            <span x-text="product.warranty"></span>
                                                        </div>
                                                    </template>

                                                    {{-- Price Section --}}
                                                    <div class="mt-auto pt-3 border-t border-gray-50">
                                                        <div class="flex items-baseline gap-2 flex-wrap">
                                                            <span class="text-lg font-bold text-indigo-700" x-text="formatMoney(product.price)"></span>
                                                            <template x-if="product.market_price > product.price">
                                                                <span class="text-xs text-gray-400 line-through font-medium" x-text="formatMoney(product.market_price)"></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Custom Navigation Buttons (Outside Slider) --}}
                            <div :class="'prev-cat-' + section.id"
                                class="absolute top-1/2 -translate-y-1/2 -left-5 z-20 w-12 h-12 bg-white rounded-full shadow-lg border border-gray-100 items-center justify-center text-gray-600 cursor-pointer opacity-0 group-hover/slider:opacity-100 transition-all duration-300 hover:bg-indigo-600 hover:text-white hover:scale-110 hidden xl:flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </div>

                            <div :class="'next-cat-' + section.id"
                                class="absolute top-1/2 -translate-y-1/2 -right-5 z-20 w-12 h-12 bg-white rounded-full shadow-lg border border-gray-100 items-center justify-center text-gray-600 cursor-pointer opacity-0 group-hover/slider:opacity-100 transition-all duration-300 hover:bg-indigo-600 hover:text-white hover:scale-110 hidden xl:flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- KỊCH BẢN JS GIỮ NGUYÊN 100% --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('main-banner-swiper')) {
                new Swiper('#main-banner-swiper', {
                    loop: true,
                    speed: 800,
                    effect: 'fade', // Thêm hiệu ứng fade cho mượt
                    fadeEffect: { crossFade: true },
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
                                spaceBetween: 16,
                                observer: true,
                                observeParents: true,
                                navigation: {
                                    nextEl: '.next-cat-' + section.id,
                                    prevEl: '.prev-cat-' + section.id,
                                },
                                breakpoints: {
                                    640: { slidesPerView: 2, spaceBetween: 20 },
                                    768: { slidesPerView: 3, spaceBetween: 24 },
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
