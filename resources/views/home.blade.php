@extends('layouts.app')

@section('content')
    {{-- 1. Hero Banner (Quảng cáo chính) --}}
    <div class="relative group">
        <div class="swiper w-full" id="main-banner-swiper">
            <div class="swiper-wrapper">
                @foreach($banners as $banner)
                    <div class="swiper-slide">
                        <a href="{{ $banner->url ?? '#' }}"
                            class="block w-full h-[300px] md:h-[450px] relative overflow-hidden group">
                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">

                            {{-- Overlay gradient (Chỉ hiện khi có title) --}}
                            @if($banner->title)
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex items-end">
                                    <div class="container mx-auto px-4 py-12">
                                        <h2 class="text-white text-3xl md:text-5xl font-bold drop-shadow-lg mb-4 transform translate-y-4 opacity-0 transition-all duration-500"
                                            style="animation: fadeUp 0.8s forwards 0.3s">
                                            {{ $banner->title }}
                                        </h2>
                                        <span
                                            class="inline-block bg-white text-black px-6 py-3 rounded-full font-bold text-sm hover:bg-indigo-600 hover:text-white transition cursor-pointer shadow-lg transform translate-y-4 opacity-0"
                                            style="animation: fadeUp 0.8s forwards 0.5s">
                                            Khám phá ngay
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>

            {{-- Pagination & Navigation --}}
            <div class="swiper-pagination"></div>
            <div
                class="swiper-button-prev !text-white !w-12 !h-12 !bg-black/30 !rounded-full !backdrop-blur-sm hover:!bg-indigo-600 transition after:!text-xl hidden md:flex">
            </div>
            <div
                class="swiper-button-next !text-white !w-12 !h-12 !bg-black/30 !rounded-full !backdrop-blur-sm hover:!bg-indigo-600 transition after:!text-xl hidden md:flex">
            </div>
        </div>
    </div>

    {{-- 2. Danh sách Sản phẩm theo Danh mục --}}
    <div class="bg-gray-50 py-12 space-y-16">
        <div class="container mx-auto px-4">

            {{-- Loop qua từng danh mục được truyền từ Controller --}}
            @foreach($product_category[0] as $category)
                {{-- Chỉ hiển thị nếu danh mục có sản phẩm --}}
                @if(isset($category['products']) && count($category['products']) > 0)

                    <div class="relative bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-12">

                        {{-- Header của mỗi Block Danh mục --}}
                        <div
                            class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 border-b border-gray-100 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-8 bg-indigo-600 rounded-full"></div>
                                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">
                                    <a href="{{ $category['url'] }}" class="hover:text-indigo-600 transition-colors">
                                        {{ $category['name'] }}
                                    </a>
                                </h2>
                            </div>
                            <a href="{{ $category['url'] }}"
                                class="group flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition bg-indigo-50 px-4 py-2 rounded-full hover:bg-indigo-100">
                                Xem tất cả
                                <i data-lucide="arrow-right"
                                    class="w-4 h-4 transform group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>

                        {{-- Slider Sản phẩm --}}
                        <div class="relative group/slider px-2">
                            <div class="swiper product-swiper" id="swiper-cat-{{ $category['id'] }}">
                                <div class="swiper-wrapper pb-12">
                                    @foreach($category['products'] as $product)
                                        <div class="swiper-slide h-auto">
                                            {{-- Card Sản phẩm --}}
                                            <div
                                                class="bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-xl hover:border-indigo-100 transition-all duration-300 h-full flex flex-col relative group/card">

                                                {{-- Badge Giảm giá --}}
                                                @if($product->cost_price > $product->price)
                                                    <span
                                                        class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm z-10">
                                                        -{{ round((($product->cost_price - $product->price) / $product->cost_price) * 100) }}%
                                                    </span>
                                                @endif

                                                {{-- Ảnh Sản phẩm --}}
                                                <a href="{{ route('admin.products.show', $product->id) }}"
                                                    class="block relative pt-[100%] bg-gray-50 overflow-hidden">
                                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                        class="absolute top-0 left-0 w-full h-full object-contain p-6 transition-transform duration-500 group-hover/card:scale-110 mix-blend-multiply">
                                                </a>

                                                {{-- Nội dung Card --}}
                                                <div class="p-4 flex flex-col flex-grow">
                                                    <div class="mb-2">
                                                        <span
                                                            class="text-[10px] text-indigo-500 uppercase font-bold tracking-wider bg-indigo-50 px-2 py-0.5 rounded">{{ $category['name'] }}</span>
                                                    </div>
                                                    <h3
                                                        class="text-sm font-semibold text-gray-800 line-clamp-2 mb-3 flex-grow h-[40px]">
                                                        <a href="{{ route('admin.products.show', $product->id) }}"
                                                            class="hover:text-indigo-600 transition-colors"
                                                            title="{{ $product->name }}">
                                                            {{ $product->name }}
                                                        </a>
                                                    </h3>

                                                    <div class="mt-auto pt-4 border-t border-gray-50">
                                                        <div class="flex items-baseline gap-2 mb-3 flex-wrap">
                                                            <span
                                                                class="text-lg font-bold text-indigo-600">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                                            @if($product->cost_price > $product->price)
                                                                <span
                                                                    class="text-xs text-gray-400 line-through">{{ number_format($product->cost_price, 0, ',', '.') }}đ</span>
                                                            @endif
                                                        </div>

                                                        {{-- Nút Thêm vào giỏ --}}
                                                        <form action="{{ route('cart.add') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                            <button type="submit"
                                                                class="w-full py-2.5 bg-gray-100 text-gray-700 font-medium text-sm rounded-lg hover:bg-indigo-600 hover:text-white hover:shadow-md hover:shadow-indigo-200 transition-all flex items-center justify-center gap-2 group/btn active:scale-95">
                                                                <i data-lucide="shopping-bag"
                                                                    class="w-4 h-4 transition-transform group-hover/btn:-translate-y-0.5"></i>
                                                                Thêm vào giỏ
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                {{-- Pagination của mỗi Slider --}}
                                <div class="swiper-pagination !bottom-0"></div>
                            </div>

                            {{-- Nút điều hướng Slider (Hiện khi hover vào block) --}}
                            <button
                                class="prev-btn-{{ $category['id'] }} absolute top-1/2 -left-4 md:-left-6 z-10 w-10 h-10 md:w-12 md:h-12 bg-white text-gray-800 rounded-full shadow-lg border border-gray-100 flex items-center justify-center hover:text-indigo-600 hover:scale-110 transition-all opacity-0 group-hover/slider:opacity-100 disabled:opacity-0 disabled:cursor-not-allowed">
                                <i data-lucide="chevron-left" class="w-6 h-6"></i>
                            </button>
                            <button
                                class="next-btn-{{ $category['id'] }} absolute top-1/2 -right-4 md:-right-6 z-10 w-10 h-10 md:w-12 md:h-12 bg-white text-gray-800 rounded-full shadow-lg border border-gray-100 flex items-center justify-center hover:text-indigo-600 hover:scale-110 transition-all opacity-0 group-hover/slider:opacity-100 disabled:opacity-0 disabled:cursor-not-allowed">
                                <i data-lucide="chevron-right" class="w-6 h-6"></i>
                            </button>
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Nếu không có danh mục nào (Empty State) --}}
            @if(empty($product_category[0]) || count($product_category[0]) === 0)
                <div class="text-center py-20">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <i data-lucide="package-open" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Chưa có sản phẩm nào</h3>
                    <p class="text-gray-500 mt-1">Hãy quay lại sau để xem các ưu đãi mới nhất nhé!</p>
                </div>
            @endif

        </div>
    </div>

    {{-- Animation Styles (Inline để đảm bảo load ngay) --}}
    <style>
        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .swiper-pagination-bullet-active {
            background-color: #4f46e5 !important;
            /* Indigo 600 */
            width: 20px !important;
            border-radius: 4px !important;
        }
    </style>
@endsection
