<div x-data="{
    mobileMenuOpen: false,
    categoryPopupOpen: false,
    activeCategory: {{ $menuCategories->first()->id ?? 0 }}
}" class="contents font-sans text-slate-800">

    {{-- 1. TOP BANNER (Swiper) --}}
    <div class="relative bg-slate-900 h-10 overflow-hidden z-[60]">
        <div class="swiper-container h-full" id="top-banner-swiper">
            <div class="swiper-wrapper">
                @forelse($headerBanners ?? [] as $banner)
                    @php $banner = (object) $banner; @endphp
                    <a href="{{ $banner->url ?? '#' }}"
                        class="swiper-slide flex items-center justify-center w-full h-full group">
                        @if(isset($banner->image_url) && $banner->image_url)
                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title ?? '' }}"
                                class="h-full w-full object-cover opacity-90 group-hover:opacity-100 transition-opacity">
                        @else
                            <span
                                class="text-xs font-semibold text-white tracking-wide uppercase">{{ $banner->title ?? 'Welcome to Ultimate Store' }}</span>
                        @endif
                    </a>
                @empty
                    <div class="swiper-slide flex items-center justify-center text-xs text-white font-medium">
                        Chào mừng đến với {{ config('app.name') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 2. HOT LINKS BAR (Marquee) --}}
    <div class="bg-indigo-50/80 backdrop-blur-sm border-b border-indigo-100 hidden md:block relative z-[55]">
        <div class="container mx-auto px-4 h-9 flex items-center justify-between text-xs font-medium">
            {{-- Marquee bên trái --}}
            <div class="flex-grow min-w-0 overflow-hidden w-1/2 relative text-indigo-900">
                <div class="animate-marquee whitespace-nowrap inline-block hover:pause">
                    <span class="inline-flex items-center gap-8">
                        <a href="#" class="flex items-center gap-2 hover:text-indigo-600 transition-colors">
                            <i data-lucide="zap" class="w-3.5 h-3.5 text-yellow-500 fill-yellow-500"></i>
                            <span>Khuyến mãi HOT: Giảm giá 20% cho toàn bộ Màn hình!</span>
                        </a>
                        <a href="#" class="flex items-center gap-2 hover:text-indigo-600 transition-colors">
                            <i data-lucide="truck" class="w-3.5 h-3.5 text-blue-500"></i>
                            <span>Miễn phí vận chuyển cho đơn hàng trên 2 triệu.</span>
                        </a>
                        <a href="#" class="flex items-center gap-2 hover:text-indigo-600 transition-colors">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-green-500"></i>
                            <span>Hàng chính hãng, bảo hành lên tới 36 tháng.</span>
                        </a>
                    </span>
                </div>
            </div>

            {{-- Links bên phải --}}
            <div class="flex items-center gap-x-4 flex-shrink-0 text-slate-600 pl-4 bg-transparent z-10">
                <a href="#" class="flex items-center gap-1.5 hover:text-indigo-600 transition-colors">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5"></i><span>Hệ thống Showroom</span>
                </a>
                <span class="text-indigo-200">|</span>
                <a href="/tin-tuc" class="flex items-center gap-1.5 hover:text-indigo-600 transition-colors">
                    <i data-lucide="newspaper" class="w-3.5 h-3.5"></i><span>Tin công nghệ</span>
                </a>
                <span class="text-indigo-200">|</span>
                <a href="#" class="flex items-center gap-1.5 hover:text-indigo-600 transition-colors">
                    <i data-lucide="life-buoy" class="w-3.5 h-3.5"></i><span>Hỗ trợ</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 3. MAIN HEADER--}}
    <header
        class="bg-white/90 backdrop-blur-md sticky top-0 z-50 transition-all duration-300 w-full border-b border-slate-100 shadow-sm">
        <div class="container mx-auto px-4 h-[88px] flex items-center justify-between gap-6">

            {{-- Logo Area --}}
            <div class="flex items-center gap-4">
                {{-- Mobile Menu Trigger --}}
                <button @click="mobileMenuOpen = true"
                    class="md:hidden p-2 text-slate-600 hover:text-indigo-600 transition-colors">
                    <i data-lucide="menu" class="w-7 h-7"></i>
                </button>

                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div
                        class="relative w-10 h-10 flex items-center justify-center bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-200 group-hover:scale-105 transition-transform duration-300">
                        <i data-lucide="cpu" class="w-6 h-6"></i>
                        <div
                            class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-400 rounded-full border-2 border-white">
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span
                            class="text-2xl font-extrabold text-slate-900 leading-none tracking-tighter">ULTIMATE</span>
                        <span class="text-[10px] font-bold text-indigo-600 tracking-[0.2em] uppercase mt-0.5">Technology
                            Store</span>
                    </div>
                </a>
            </div>

            {{-- Mega Menu Button (Desktop) --}}
            <div class="hidden md:block relative group" @mouseleave="categoryPopupOpen = false">
                <button @mouseover="categoryPopupOpen = true" @click="categoryPopupOpen = !categoryPopupOpen"
                    class="h-11 flex items-center gap-2 px-5 bg-slate-100 hover:bg-indigo-50 text-slate-700 hover:text-indigo-700 rounded-full transition-all font-bold text-sm border border-transparent hover:border-indigo-100">
                    <i data-lucide="layout-grid" class="w-5 h-5"></i>
                    <span>Danh mục</span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 opacity-50"></i>
                </button>

                {{-- MEGA MENU POPUP BODY --}}
                <div x-show="categoryPopupOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="absolute top-full left-0 z-[60] w-[900px] pt-4" style="display: none;">

                    <div
                        class="bg-white shadow-2xl rounded-2xl border border-slate-100 flex overflow-hidden min-h-[480px] ring-1 ring-black/5">
                        {{-- Cột trái: Danh mục cha --}}
                        <div
                            class="w-[30%] bg-slate-50/80 border-r border-slate-100 py-3 overflow-y-auto max-h-[500px]">
                            @foreach($menuCategories ?? [] as $cat)
                                @php $cat = (object) $cat; @endphp
                                <a href="{{ route('category.show', $cat->slug) }}"
                                    @mouseover="activeCategory = {{ $cat->id }}"
                                    :class="activeCategory === {{ $cat->id }} ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
                                    class="mx-3 my-1 px-4 py-3 rounded-lg flex items-center justify-between text-sm font-semibold transition-all duration-200 group">
                                    <span class="flex items-center gap-3">
                                        {{-- Icon giả lập --}}
                                        <i data-lucide="box" class="w-4 h-4"
                                            :class="activeCategory === {{ $cat->id }} ? 'text-indigo-500' : 'text-slate-400'"></i>
                                        {{ $cat->name }}
                                    </span>
                                    <i data-lucide="chevron-right"
                                        class="w-4 h-4 text-slate-300 group-hover:text-indigo-400"
                                        :class="activeCategory === {{ $cat->id }} ? 'text-indigo-500 opacity-100' : 'opacity-0 group-hover:opacity-100'"></i>
                                </a>
                            @endforeach
                        </div>

                        {{-- Cột phải: Danh mục con (Grid) --}}
                        <div class="w-[70%] p-8 bg-white overflow-y-auto max-h-[500px]">
                            @foreach($menuCategories ?? [] as $cat)
                                @php $cat = (object) $cat; @endphp
                                <div x-show="activeCategory === {{ $cat->id }}" class="h-full flex flex-col animate-fadeIn">
                                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                                        <h3 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $cat->name }}</h3>
                                        <a href="{{ route('category.show', $cat->slug) }}"
                                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 group/link">
                                            Xem tất cả <i data-lucide="arrow-right"
                                                class="w-4 h-4 transition-transform group-hover/link:translate-x-1"></i>
                                        </a>
                                    </div>

                                    @if(isset($cat->children) && count($cat->children) > 0)
                                        <div class="grid grid-cols-3 gap-y-6 gap-x-4">
                                            @foreach($cat->children as $child)
                                                @php $child = (object) $child; @endphp
                                                <a href="{{ route('category.show', $child->slug) }}"
                                                    class="group flex items-center gap-3 p-2 rounded-xl hover:bg-indigo-50/50 transition-all">
                                                    <div
                                                        class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-sm border border-slate-200 group-hover:border-indigo-200 group-hover:text-indigo-600 group-hover:bg-white transition-colors">
                                                        {{ substr($child->name, 0, 1) }}
                                                    </div>
                                                    <span
                                                        class="text-sm font-medium text-slate-600 group-hover:text-indigo-700 line-clamp-2">
                                                        {{ $child->name }}
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="flex-1 flex flex-col items-center justify-center text-slate-300 h-full">
                                            <i data-lucide="package-open" class="w-20 h-20 mb-4 opacity-50 stroke-1"></i>
                                            <p class="text-base font-medium">Đang cập nhật danh mục con</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Search Bar (Giữa) --}}
            <div class="flex-1 max-w-2xl mx-4 hidden md:block relative z-50" x-data="{
        query: '{{ request('q') }}',
        suggestions: [],
        open: false,
        loading: false,

        async search() {
            if (this.query.length < 2) {
                this.suggestions = [];
                this.open = false;
                return;
            }

            this.loading = true;
            try {
                // Gọi API Search Suggestions
                let response = await fetch(`{{ route('search.suggestions') }}?q=${encodeURIComponent(this.query)}`);
                let json = await response.json();

                if (json.success && json.data.length > 0) {
                    this.suggestions = json.data;
                    this.open = true;
                } else {
                    this.suggestions = [];
                    this.open = false;
                }
            } catch (error) {
                console.error('Search error:', error);
            } finally {
                this.loading = false;
            }
        }
     }" @click.away="open = false">

                <form method="GET" action="{{ route('search.index') }}" class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-lucide="search" class="h-5 w-5 text-slate-400"></i>
                    </div>

                    {{-- Input Search --}}
                    <input type="text" name="q" x-model="query" @input.debounce.300ms="search()"
                        @focus="if(suggestions.length > 0) open = true" placeholder="Bạn đang tìm kiếm sản phẩm gì?"
                        class="block w-full pl-12 pr-12 py-3 rounded-full border-0 bg-slate-100 text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all shadow-sm text-sm font-medium"
                        autocomplete="off" />

                    {{-- Loading Indicator --}}
                    <div x-show="loading" class="absolute right-14 top-3.5" style="display: none;">
                        <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>

                    <button type="submit"
                        class="absolute right-2 top-1.5 bottom-1.5 px-4 bg-white text-indigo-600 font-bold text-xs rounded-full shadow-sm border border-slate-100 hover:bg-indigo-50 transition-colors flex items-center">
                        Tìm
                    </button>

                    {{-- Suggestions Dropdown --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden divide-y divide-slate-50"
                        style="display: none;">

                        <template x-for="product in suggestions" :key="product.url">
                            <a :href="product.url"
                                class="flex items-center gap-4 px-4 py-3 hover:bg-slate-50 transition-colors group">
                                <img :src="product.image"
                                    class="w-10 h-10 object-cover rounded-lg border border-slate-200"
                                    alt="Product Image">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate group-hover:text-indigo-600"
                                        x-text="product.name"></p>
                                    <p class="text-xs text-red-500 font-bold mt-0.5" x-text="product.price"></p>
                                </div>
                                <i data-lucide="chevron-right"
                                    class="w-4 h-4 text-slate-300 group-hover:text-indigo-400"></i>
                            </a>
                        </template>

                        <div class="bg-slate-50 px-4 py-2.5 text-center">
                            <button type="submit"
                                class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                                Xem tất cả kết quả cho "<span x-text="query"></span>"
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Header Actions (Phải) --}}
            <div class="flex items-center gap-2 md:gap-5">

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}"
                    class="relative group flex items-center justify-center w-11 h-11 rounded-full hover:bg-slate-100 transition-all"
                    x-data="{ count: {{ $cartCount ?? 0 }}, updateCount(e) { this.count = e.detail.count; this.$el.classList.add('animate-bounce'); setTimeout(()=>this.$el.classList.remove('animate-bounce'), 1000); } }"
                    @cart-updated.window="updateCount($event)">

                    <i data-lucide="shopping-bag"
                        class="w-6 h-6 text-slate-600 group-hover:text-indigo-600 transition-colors"></i>
                    <span x-show="count > 0"
                        class="absolute -top-1 -right-1 h-5 min-w-[20px] px-1 flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold border-2 border-white shadow-sm"
                        x-text="count > 99 ? '99+' : count">
                        {{ ($cartCount ?? 0) > 99 ? '99+' : ($cartCount ?? 0) }}
                    </span>
                </a>

                {{-- User Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    @auth
                        <button @click="open = !open"
                            class="flex items-center gap-3 pl-1 pr-2 py-1 rounded-full hover:bg-slate-100 transition-all border border-transparent hover:border-slate-200">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                                    class="w-9 h-9 rounded-full object-cover ring-2 ring-white shadow-sm">
                            @else
                                <div
                                    class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="hidden lg:flex flex-col items-start">
                                <span class="text-xs text-slate-500 font-medium leading-tight">Xin chào,</span>
                                <span
                                    class="text-sm font-bold text-slate-800 leading-tight max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                            </div>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 divide-y divide-slate-100"
                            style="display: none;">
                            <div class="px-5 py-3">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tài khoản</p>
                                <p class="text-sm font-bold text-slate-800 truncate mt-1">{{ Auth::user()->email }}</p>
                            </div>

                            <div class="py-1">
                                @if(Auth::user()->hasRole('admin'))
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Trang quản trị
                                    </a>
                                @endif
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                    <i data-lucide="user" class="w-4 h-4"></i> Hồ sơ cá nhân
                                </a>
                                <a href="{{ route('customer.orders.index') }}"
                                    class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                    <i data-lucide="package" class="w-4 h-4"></i> Đơn hàng của tôi
                                </a>
                            </div>

                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                        <i data-lucide="log-out" class="w-4 h-4"></i> Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-3">
                            <a href="{{ route('login') }}"
                                class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors hidden sm:block">Đăng
                                nhập</a>
                            <a href="{{ route('register') }}"
                                class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-full shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                                Đăng ký
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- 4. MOBILE MENU (Sidebar) --}}
    <div class="relative z-[100]" x-show="mobileMenuOpen" style="display: none;">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" x-show="mobileMenuOpen"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="mobileMenuOpen = false">
        </div>

        {{-- Panel --}}
        <div class="fixed inset-y-0 left-0 w-80 bg-white shadow-2xl transform transition-transform overflow-y-auto z-[101]"
            x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full">

            <div class="p-5 flex items-center justify-between border-b border-slate-100 bg-slate-50">
                <span class="font-extrabold text-lg text-slate-800 flex items-center gap-2">
                    <span class="text-indigo-600">MENU</span> CHÍNH
                </span>
                <button @click="mobileMenuOpen = false"
                    class="p-2 bg-white rounded-full border border-slate-200 text-slate-500 hover:text-red-600 hover:border-red-200 shadow-sm transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            {{-- Mobile Search --}}
            <div class="p-4 border-b border-slate-100">
                <form action="{{ route('search.index') }}" method="get" class="relative">
                    <i data-lucide="search" class="absolute left-3 top-3 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="q" placeholder="Tìm kiếm..."
                        class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 text-sm bg-slate-50 focus:bg-white transition-colors">
                </form>
            </div>

            {{-- Mobile Navigation --}}
            <nav class="p-3 space-y-1">
                @foreach($menuCategories ?? [] as $cat)
                    @php $cat = (object) $cat; @endphp
                    <div x-data="{ expanded: false }"
                        class="border border-transparent rounded-xl hover:bg-slate-50 hover:border-slate-100 transition-all">
                        <div class="flex items-center justify-between px-3 py-3">
                            <a href="{{ route('category.show', $cat->slug) }}"
                                class="flex-1 font-bold text-slate-700 flex items-center gap-3">
                                <span
                                    class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-xs font-bold text-indigo-600">
                                    {{ substr($cat->name, 0, 1) }}
                                </span>
                                {{ $cat->name }}
                            </a>
                            @if(isset($cat->children) && count($cat->children) > 0)
                                <button @click="expanded = !expanded"
                                    class="p-2 text-slate-400 hover:text-indigo-600 transition-colors">
                                    <i data-lucide="chevron-down" class="w-5 h-5 transition-transform duration-200"
                                        :class="expanded ? 'rotate-180' : ''"></i>
                                </button>
                            @endif
                        </div>

                        @if(isset($cat->children) && count($cat->children) > 0)
                            <div x-show="expanded" x-collapse class="pl-12 pr-2 pb-2 space-y-1">
                                @foreach($cat->children as $child)
                                    @php $child = (object) $child; @endphp
                                    <a href="{{ route('category.show', $child->slug) }}"
                                        class="block px-3 py-2 text-sm font-medium text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors border-l-2 border-transparent hover:border-indigo-300">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="border-t border-slate-100 my-4 mx-2"></div>

                <a href="/tin-tuc"
                    class="flex items-center px-3 py-3 font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-colors">
                    <i data-lucide="newspaper" class="w-5 h-5 mr-3 text-slate-400"></i> Tin tức công nghệ
                </a>
                <a href="#"
                    class="flex items-center px-3 py-3 font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-colors">
                    <i data-lucide="phone" class="w-5 h-5 mr-3 text-slate-400"></i> Liên hệ Hotline
                </a>
            </nav>
        </div>
    </div>
</div>
