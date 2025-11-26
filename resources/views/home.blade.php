@extends('layouts.app')

@section('content')
    {{-- Alpine Component --}}
    <div x-data="homePageHandler()" x-init="initData()">

        {{-- Loading Spinner --}}
        <div x-show="isLoading" class="flex justify-center items-center h-96">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-indigo-600"></div>
        </div>

        {{-- Main Content (Ẩn khi đang load để tránh giật layout) --}}
        <div x-show="!isLoading" style="display: none;">

            {{-- 1. Hero Banner Slider --}}
            <div class="relative group mb-8">
                <div class="swiper w-full h-[300px] md:h-[450px]" id="main-banner-swiper">
                    <div class="swiper-wrapper">
                        <template x-for="banner in banners" :key="banner.id">
                            <div class="swiper-slide">
                                <a :href="banner.url || '#'" class="block w-full h-full relative overflow-hidden group">
                                    <img :src="banner.image_url" :alt="banner.title" class="w-full h-full object-cover">

                                    {{-- Overlay Title --}}
                                    <template x-if="banner.title">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex items-end">
                                            <div class="container mx-auto px-4 py-12">
                                                <h2 class="text-white text-2xl md:text-4xl font-bold drop-shadow-md"
                                                    x-text="banner.title"></h2>
                                            </div>
                                        </div>
                                    </template>
                                </a>
                            </div>
                        </template>
                    </div>
                    {{-- Nav Buttons --}}
                    <div class="swiper-pagination"></div>
                    <div
                        class="swiper-button-prev !text-white !w-10 !h-10 !bg-black/20 !rounded-full backdrop-blur-sm hover:!bg-indigo-600 transition hidden md:flex after:!text-lg">
                    </div>
                    <div
                        class="swiper-button-next !text-white !w-10 !h-10 !bg-black/20 !rounded-full backdrop-blur-sm hover:!bg-indigo-600 transition hidden md:flex after:!text-lg">
                    </div>
                </div>
            </div>


            {{-- 2. Danh sách Sản phẩm theo Block --}}
            <div class="bg-gray-50 py-12 space-y-16">
                <div class="container mx-auto px-4">
                    <template x-for="section in sections" :key="section.id">
                        <div class="mb-12" x-show="section.products && section.products.length > 0">

                            {{-- Block Header --}}
                            <div class="flex justify-between items-end mb-6 px-2">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-800" x-text="section.name"></h2>
                                    <div class="h-1 w-20 bg-indigo-600 mt-2 rounded-full"></div>
                                </div>
                                <a :href="section.url"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                    Xem tất cả <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </a>
                            </div>

                            {{-- Slider Container --}}
                            <div class="relative group/slider">
                                <div class="swiper product-swiper px-2 py-4" :id="'swiper-cat-' + section.id">
                                    <div class="swiper-wrapper">
                                        <template x-for="product in section.products" :key="product.id">
                                            <div class="swiper-slide h-auto">
                                                {{-- Product Card --}}
                                                <div
                                                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 h-full flex flex-col relative group/card">

                                                    {{-- Badge Sale --}}
                                                    <template x-if="product.cost_price > product.price">
                                                        <span
                                                            class="absolute top-3 right-3 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-lg z-10 shadow-sm">
                                                            <span
                                                                x-text="'-' + Math.round(((product.cost_price - product.price) / product.cost_price) * 100) + '%'"></span>
                                                        </span>
                                                    </template>

                                                    {{-- Image --}}
                                                    <a :href="product.detail_url"
                                                        class="block relative pt-[100%] bg-white overflow-hidden">
                                                        <img :src="product.image_url" :alt="product.name"
                                                            class="absolute top-0 left-0 w-full h-full object-contain p-4 transition-transform duration-500 group-hover/card:scale-110">
                                                    </a>

                                                    {{-- Info --}}
                                                    <div class="p-4 flex flex-col flex-grow">
                                                        <h3
                                                            class="text-sm font-medium text-gray-800 line-clamp-2 mb-2 flex-grow min-h-[40px]">
                                                            <a :href="product.detail_url"
                                                                class="hover:text-indigo-600 transition-colors"
                                                                x-text="product.name"></a>
                                                        </h3>

                                                        <div class="mt-auto">
                                                            <div class="flex items-baseline gap-2 mb-3">
                                                                <span class="text-lg font-bold text-indigo-600"
                                                                    x-text="formatMoney(product.price)"></span>
                                                                <template x-if="product.cost_price > product.price">
                                                                    <span class="text-xs text-gray-400 line-through"
                                                                        x-text="formatMoney(product.cost_price)"></span>
                                                                </template>
                                                            </div>

                                                            <button @click="addToCart(product.id)"
                                                                class="w-full py-2 bg-gray-100 hover:bg-indigo-600 text-gray-700 hover:text-white font-medium text-sm rounded-lg transition-colors flex items-center justify-center gap-2">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                    height="16" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <circle cx="8" cy="21" r="1" />
                                                                    <circle cx="19" cy="21" r="1" />
                                                                    <path
                                                                        d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                                                                </svg>
                                                                Thêm vào giỏ
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="swiper-pagination !bottom-0"></div>
                                </div>

                                {{-- Nav Buttons for Product Slider --}}
                                <div :class="'swiper-button-prev prev-cat-' + section.id"
                                    class="!hidden md:!flex !w-10 !h-10 !bg-white !text-gray-800 !shadow-md !rounded-full !-left-4 opacity-0 group-hover/slider:opacity-100 transition-opacity after:!text-lg">
                                </div>
                                <div :class="'swiper-button-next next-cat-' + section.id"
                                    class="!hidden md:!flex !w-10 !h-10 !bg-white !text-gray-800 !shadow-md !rounded-full !-right-4 opacity-0 group-hover/slider:opacity-100 transition-opacity after:!text-lg">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function homePageHandler() {
            return {
                isLoading: true,
                banners: [],
                sections: [],
                menu: [],

                async initData() {
                    try {
                        // Gọi API lấy dữ liệu
                        const response = await axios.get('{{ route("home") }}', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });

                        if (response.data.success) {
                            this.banners = response.data.data.banners;
                            this.sections = response.data.data.sections;
                            this.menu = response.data.data.menu;
                        }
                    } catch (error) {
                        console.error('Failed to fetch home data:', error);
                    } finally {
                        this.isLoading = false;
                        // Chờ DOM render xong mới khởi tạo Swiper
                        this.$nextTick(() => {
                            this.initSwipers();
                            // Re-init icons nếu dùng Lucide
                            if (typeof lucide !== 'undefined') lucide.createIcons();
                        });
                    }
                },

                formatMoney(amount) {
                    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
                },

                async addToCart(productId) {
                    try {
                        const res = await axios.post('{{ route("cart.add") }}', {
                            product_id: productId,
                            quantity: 1
                        });
                        if (res.data.success) {
                            alert('Đã thêm sản phẩm vào giỏ hàng!');
                            // Reload trang để update số lượng trên header (hoặc dùng event bus)
                            window.location.reload();
                        }
                    } catch (e) {
                        alert('Lỗi: ' + (e.response?.data?.message || 'Không thể thêm vào giỏ'));
                    }
                },

                initSwipers() {
                    // 1. Main Banner
                    if (document.getElementById('main-banner-swiper')) {
                        new Swiper('#main-banner-swiper', {
                            loop: false,
                            rewind: true,
                            speed: 800,
                            autoplay: { delay: 5000, disableOnInteraction: false },
                            pagination: { el: '.swiper-pagination', clickable: true },
                            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                        });
                    }

                    // 2. Product Sliders
                    this.sections.forEach(section => {
                        const el = document.getElementById('swiper-cat-' + section.id);
                        if (el && section.products.length > 0) {
                            new Swiper(el, {
                                slidesPerView: 2, // Mặc định mobile 2 cột
                                spaceBetween: 16,
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
                }
            }
        }
    </script>
@endsection
