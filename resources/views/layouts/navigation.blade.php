{{-- 1. Top Banner (Swiper) --}}
<div class="relative bg-gray-900 h-10 overflow-hidden">
    <div class="swiper-container h-full" id="top-banner-swiper">
        <div class="swiper-wrapper">
            @forelse($headerBanners ?? [] as $banner)
                <div class="swiper-slide flex items-center justify-center text-xs font-medium text-white tracking-wide">
                    <a href="{{ $banner->url ?? '#' }}" class="flex items-center gap-2 w-full h-full justify-center hover:text-indigo-300 transition">
                        @if($banner->image_url)
                            <img src="{{ $banner->image_url }}" alt="" class="h-full object-cover opacity-50 hover:opacity-100 w-full absolute inset-0 z-0">
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
                    <a href="#" class="flex items-center gap-2 hover:text-indigo-600"><i data-lucide="zap" class="w-4 h-4 text-yellow-500"></i> Flash Sale: Giảm 50% màn hình!</a>
                    <a href="#" class="flex items-center gap-2 hover:text-indigo-600"><i data-lucide="truck" class="w-4 h-4 text-blue-500"></i> Freeship đơn từ 500k</a>
                    <a href="#" class="flex items-center gap-2 hover:text-indigo-600"><i data-lucide="shield-check" class="w-4 h-4 text-green-500"></i> Bảo hành chính hãng 24 tháng</a>
                    <a href="#" class="flex items-center gap-2 hover:text-indigo-600"><i data-lucide="gift" class="w-4 h-4 text-red-500"></i> Quà tặng trị giá 1 triệu đồng</a>
                </span>
            </div>
        </div>
    </div>
</div>

{{-- 3. Main Header --}}
<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="container mx-auto px-4 h-20 flex items-center justify-between gap-4">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 group">
            <div class="bg-indigo-600 text-white p-2 rounded-lg shadow-lg shadow-indigo-200 group-hover:scale-110 transition-transform duration-300">
                <i data-lucide="cpu" class="w-6 h-6"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-xl font-bold text-gray-900 leading-none tracking-tight">ULTIMATE</span>
                <span class="text-xs font-medium text-indigo-600 tracking-widest">STORE</span>
            </div>
        </a>

        {{-- Search Bar
        <div class="hidden md:block flex-1 max-w-2xl">
            <form action="{{ route('search.results') }}" method="GET" class="relative group">
                <input type="text" name="q" placeholder="Tìm kiếm sản phẩm, thương hiệu..."
                    class="w-full bg-gray-50 text-gray-900 border border-gray-200 rounded-full py-2.5 pl-5 pr-12 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all shadow-sm">
                <button type="submit" class="absolute right-1 top-1 bottom-1 bg-indigo-600 text-white p-2 rounded-full hover:bg-indigo-700 transition-colors shadow-md">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </button>
            </form>
        </div> --}}

        {{-- Actions --}}
        <div class="flex items-center gap-2 sm:gap-4">

            {{-- Cart --}}
            <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors group">
                <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                {{-- Demo Badge --}}
                <span class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold h-4 w-4 flex items-center justify-center rounded-full ring-2 ring-white transform group-hover:scale-110 transition-transform">2</span>
            </a>

            {{-- User Dropdown (Alpine) --}}
            <div class="relative" x-data="{ open: false }">
                @auth
                    <button @click="open = !open" class="flex items-center gap-2 p-1 pr-3 rounded-full border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all">
                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=6366f1&color=fff" alt="Avatar" class="w-8 h-8 rounded-full">
                        <span class="text-sm font-medium text-gray-700 hidden sm:block max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50">
                        <div class="px-4 py-2 border-b border-gray-100 bg-gray-50 rounded-t-xl">
                            <p class="text-xs text-gray-500">Xin chào,</p>
                            <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                        </div>
                        @if(Auth::user()->hasRole('admin'))
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Trang quản trị</a>
                        @endif
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Hồ sơ cá nhân</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Đăng xuất</button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-indigo-600 transition">Đăng nhập</a>
                        <span class="h-4 w-px bg-gray-300"></span>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-full shadow-md shadow-indigo-200 hover:bg-indigo-700 hover:shadow-lg transition-all">Đăng ký</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>
