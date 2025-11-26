{{-- 1. Top Banner (Swiper) --}}
<div class="relative bg-gray-900 h-10 overflow-hidden">
    <div class="swiper-container h-full" id="top-banner-swiper">
        <div class="swiper-wrapper">
            @forelse($headerBanners ?? [] as $banner)
                {{-- Cast to object to prevent non-object errors --}}
                @php $banner = (object) $banner; @endphp
                <div class="swiper-slide flex items-center justify-center text-xs font-medium text-white tracking-wide">
                    <a href="{{ $banner->url ?? '#' }}"
                        class="flex items-center gap-2 w-full h-full justify-center hover:text-indigo-300 transition">
                        @if(isset($banner->image_url) && $banner->image_url)
                            <img src="{{ $banner->image_url }}" alt=""
                                class="h-full object-cover opacity-50 hover:opacity-100 w-full absolute inset-0 z-0">
                        @endif
                        <span class="relative z-10">{{ $banner->title ?? 'Welcome to our store' }}</span>
                    </a>
                </div>
            @empty
                <div class="swiper-slide flex items-center justify-center text-xs text-white">
                    Chào mừng đến với {{ config('app.name') }}
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- 2. Marquee Links --}}
<div class="bg-indigo-50 border-b border-indigo-100 h-10 flex items-center overflow-hidden">
    <div class="container mx-auto px-4 relative w-full h-full flex items-center">
        <div class="w-full overflow-hidden">
            <div class="animate-marquee inline-block pl-full">
                <span class="inline-flex items-center gap-8 text-sm font-medium text-indigo-800">
                    <a href="#" class="flex items-center gap-2 hover:text-indigo-600"><i data-lucide="zap"
                            class="w-4 h-4 text-yellow-500"></i> Flash Sale: Giảm 50% màn hình!</a>
                    <a href="#" class="flex items-center gap-2 hover:text-indigo-600"><i data-lucide="truck"
                            class="w-4 h-4 text-blue-500"></i> Freeship đơn từ 500k</a>
                    <a href="#" class="flex items-center gap-2 hover:text-indigo-600"><i data-lucide="shield-check"
                            class="w-4 h-4 text-green-500"></i> Bảo hành chính hãng 24 tháng</a>
                    <a href="#" class="flex items-center gap-2 hover:text-indigo-600"><i data-lucide="gift"
                            class="w-4 h-4 text-red-500"></i> Quà tặng trị giá 1 triệu đồng</a>
                </span>
            </div>
        </div>
    </div>
</div>

{{-- 3. Main Header --}}
<div x-data="{ showSidebar: false }">

    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="container mx-auto px-4 h-20 flex items-center justify-between gap-4">

            <div class="flex items-center gap-6">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div
                        class="bg-indigo-600 text-white p-2 rounded-lg shadow-lg shadow-indigo-200 group-hover:scale-110 transition-transform duration-300">
                        <i data-lucide="cpu" class="w-6 h-6"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-bold text-gray-900 leading-none tracking-tight">ULTIMATE</span>
                        <span class="text-xs font-medium text-indigo-600 tracking-widest">STORE</span>
                    </div>
                </a>

                {{-- Nút Menu Danh mục (Desktop) --}}
                <button @click="showSidebar = true"
                    class="hidden md:flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 rounded-full transition-all font-medium text-sm">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                    <span>Danh mục</span>
                </button>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 sm:gap-4">
                {{-- Mobile Menu Trigger --}}
                <button @click="showSidebar = true" class="md:hidden p-2 text-gray-600 hover:text-indigo-600">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}"
                    class="relative p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors group">
                    <i data-lucide="shopping-cart" class="w-6 h-6"></i>

                    {{-- Chỉ hiện badge nếu có hàng --}}
                    @if(isset($cartCount) && $cartCount > 0)
                        <span
                            class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold h-4 w-4 flex items-center justify-center rounded-full ring-2 ring-white transform group-hover:scale-110 transition-transform">
                            {{ $cartCount > 99 ? '99+' : $cartCount }}
                        </span>
                    @endif
                </a>

                {{-- User Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    @auth
                        <button @click="open = !open"
                            class="flex items-center gap-2 p-1 pr-3 rounded-full border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all">
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=6366f1&color=fff"
                                alt="Avatar" class="w-8 h-8 rounded-full">
                            <span class="text-sm font-medium text-gray-700 hidden sm:block max-w-[100px] truncate">
                                {{ Auth::user()->name }}
                            </span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50"
                            style="display: none;">
                            <div class="px-4 py-2 border-b border-gray-100 bg-gray-50 rounded-t-xl">
                                <p class="text-xs text-gray-500">Xin chào,</p>
                                <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                            </div>
                            @if(Auth::user()->hasRole('admin'))
                                <a href="{{ route('admin.dashboard') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Trang
                                    quản trị</a>
                            @endif
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Hồ sơ
                                cá nhân</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Đăng
                                    xuất</button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}"
                                class="text-sm font-semibold text-gray-600 hover:text-indigo-600 transition">Đăng nhập</a>
                            <span class="h-4 w-px bg-gray-300"></span>
                            <a href="{{ route('register') }}"
                                class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-full shadow-md shadow-indigo-200 hover:bg-indigo-700 hover:shadow-lg transition-all">Đăng
                                ký</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- 4. OFF-CANVAS SIDEBAR MENU --}}
    <div class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" x-show="showSidebar"
        style="display: none;">

        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" x-show="showSidebar"
            x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showSidebar = false">
        </div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 left-0 flex max-w-full pr-10">

                    {{-- Panel Trượt --}}
                    <div class="pointer-events-auto w-screen max-w-xs" x-show="showSidebar"
                        x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                        x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">

                        <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                            <div class="px-4 py-6 sm:px-6 bg-indigo-600">
                                <div class="flex items-start justify-between">
                                    <h2 class="text-lg font-bold text-white" id="slide-over-title">Danh mục sản phẩm
                                    </h2>
                                    <div class="ml-3 flex h-7 items-center">
                                        <button type="button"
                                            class="rounded-md text-indigo-200 hover:text-white focus:outline-none"
                                            @click="showSidebar = false">
                                            <span class="sr-only">Close panel</span>
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Nội dung danh mục (Lấy từ AppServiceProvider) --}}
                            <div class="relative mt-6 flex-1 px-4 sm:px-6">
                                <nav class="space-y-2">
                                    @foreach($menuCategories ?? [] as $cat)
                                        {{-- Cast to object safely --}}
                                        @php $cat = (object) $cat; @endphp

                                        <div x-data="{ openSub: false }">
                                            <div class="flex justify-between items-center group">
                                                {{-- LINK TO CATEGORY PAGE --}}
                                                <a href="{{ route('category.show', $cat->slug) }}"
                                                    class="flex items-center gap-3 py-3 text-base font-medium text-gray-900 hover:text-indigo-600 flex-1">
                                                    <span
                                                        class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-sm font-bold text-gray-500 group-hover:bg-indigo-100 group-hover:text-indigo-600 transition-colors">
                                                        {{ substr($cat->name, 0, 1) }}
                                                    </span>
                                                    {{ $cat->name }}
                                                </a>

                                                {{-- Check Children --}}
                                                @php
                                                    $hasChildren = false;
                                                    if (isset($cat->children) && $cat->children instanceof \Illuminate\Support\Collection) {
                                                        $hasChildren = $cat->children->isNotEmpty();
                                                    } elseif (isset($cat->children) && is_array($cat->children)) {
                                                        $hasChildren = count($cat->children) > 0;
                                                    }
                                                @endphp

                                                @if($hasChildren)
                                                    <button @click="openSub = !openSub"
                                                        class="p-2 text-gray-400 hover:text-indigo-600">
                                                        <i data-lucide="chevron-down"
                                                            class="w-4 h-4 transition-transform duration-200"
                                                            :class="openSub ? 'rotate-180' : ''"></i>
                                                    </button>
                                                @endif
                                            </div>

                                            @if($hasChildren)
                                                <div x-show="openSub" x-collapse class="pl-11 space-y-2 pb-2">
                                                    @foreach($cat->children as $child)
                                                        @php $child = (object) $child; @endphp
                                                        <a href="{{ route('category.show', $child->slug) }}"
                                                            class="block py-1 text-sm text-gray-500 hover:text-indigo-600">
                                                            {{ $child->name }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="border-b border-gray-100 my-1"></div>
                                    @endforeach
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
