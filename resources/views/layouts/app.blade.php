<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Ultimate E-Commerce') }}</title>

    <!-- 2. Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Animation for Marquee -->
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .animate-marquee {
            display: inline-block;
            white-space: nowrap;
            animation: marquee 25s linear infinite;
        }

        .animate-marquee:hover {
            animation-play-state: paused;
        }

        /* Swiper Custom Pagination color */
        .swiper-pagination-bullet-active {
            background-color: #4f46e5 !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900 flex flex-col min-h-screen">
    {{-- <div class="min-h-screen bg-gray-100"> --}}
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-grow">
            @yield('content')
        </main>

        <!-- Page Footer -->
        <footer class="bg-slate-900 text-slate-300 border-t border-slate-800 font-sans mt-auto">
            <div class="border-b border-slate-800 bg-slate-950">
                <div class="container mx-auto px-4 py-10">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-indigo-600/20 rounded-full text-indigo-500">
                                <i data-lucide="mail" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-lg">Đăng ký nhận tin</h3>
                                <p class="text-sm text-slate-400">Nhận thông tin khuyến mãi mới nhất từ chúng tôi.</p>
                            </div>
                        </div>
                        <div class="w-full md:w-auto flex-1 max-w-lg">
                            <form class="flex gap-2">
                                <input type="email" placeholder="Địa chỉ email của bạn..."
                                    class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white placeholder:text-slate-500 outline-none transition-all">
                                <button
                                    class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg transition-colors whitespace-nowrap">
                                    Đăng ký
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-4 py-12 md:py-16">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                    <div class="space-y-4">
                        <a href="/" class="flex items-center gap-2 mb-4">
                            <div class="bg-indigo-600 text-white p-1.5 rounded-lg">
                                <i data-lucide="cpu" class="w-5 h-5"></i>
                            </div>
                            <span class="text-xl font-bold text-white tracking-tight">ULTIMATE<span
                                    class="text-indigo-500">STORE</span></span>
                        </a>
                        <p class="text-sm leading-relaxed text-slate-400">
                            Hệ thống bán lẻ các sản phẩm công nghệ chính hãng hàng đầu Việt Nam. Cam kết chất lượng, giá
                            cả cạnh tranh và dịch vụ hậu mãi tốt nhất.
                        </p>
                        <div class="flex gap-3 pt-2">
                            <a href="#"
                                class="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all"><i
                                    data-lucide="facebook" class="w-4 h-4"></i></a>
                            <a href="#"
                                class="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center hover:bg-pink-600 hover:text-white transition-all"><i
                                    data-lucide="instagram" class="w-4 h-4"></i></a>
                            <a href="#"
                                class="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all"><i
                                    data-lucide="youtube" class="w-4 h-4"></i></a>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-white font-bold text-base mb-6 relative inline-block">
                            Về Chúng Tôi
                            <span class="absolute -bottom-2 left-0 w-1/2 h-0.5 bg-indigo-600"></span>
                        </h4>
                        <ul class="space-y-3 text-sm">
                            <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center gap-2"><i
                                        data-lucide="chevron-right" class="w-3 h-3 text-slate-600"></i> Giới thiệu công
                                    ty</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center gap-2"><i
                                        data-lucide="chevron-right" class="w-3 h-3 text-slate-600"></i> Hệ thống cửa
                                    hàng</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center gap-2"><i
                                        data-lucide="chevron-right" class="w-3 h-3 text-slate-600"></i> Tuyển dụng</a>
                            </li>
                            <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center gap-2"><i
                                        data-lucide="chevron-right" class="w-3 h-3 text-slate-600"></i> Tin tức công
                                    nghệ</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-white font-bold text-base mb-6 relative inline-block">
                            Chính Sách
                            <span class="absolute -bottom-2 left-0 w-1/2 h-0.5 bg-indigo-600"></span>
                        </h4>
                        <ul class="space-y-3 text-sm">
                            <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center gap-2"><i
                                        data-lucide="chevron-right" class="w-3 h-3 text-slate-600"></i> Chính sách bảo
                                    hành</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center gap-2"><i
                                        data-lucide="chevron-right" class="w-3 h-3 text-slate-600"></i> Chính sách đổi
                                    trả</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center gap-2"><i
                                        data-lucide="chevron-right" class="w-3 h-3 text-slate-600"></i> Hướng dẫn thanh
                                    toán</a></li>
                            <li><a href="#" class="hover:text-indigo-400 transition-colors flex items-center gap-2"><i
                                        data-lucide="chevron-right" class="w-3 h-3 text-slate-600"></i> Chính sách bảo
                                    mật</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-white font-bold text-base mb-6 relative inline-block">
                            Liên Hệ
                            <span class="absolute -bottom-2 left-0 w-1/2 h-0.5 bg-indigo-600"></span>
                        </h4>
                        <ul class="space-y-4 text-sm">
                            <li class="flex items-start gap-3">
                                <i data-lucide="map-pin" class="w-5 h-5 text-indigo-500 mt-0.5 flex-shrink-0"></i>
                                <span>Tầng 5, Tòa nhà Techno, Quận Cầu Giấy, Hà Nội.</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i data-lucide="phone" class="w-5 h-5 text-indigo-500 flex-shrink-0"></i>
                                <span class="font-bold text-white text-lg">1900 6789</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i data-lucide="mail" class="w-5 h-5 text-indigo-500 flex-shrink-0"></i>
                                <a href="mailto:support@ultimate.vn"
                                    class="hover:text-white transition-colors">support@ultimate.vn</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-slate-950 py-6 border-t border-slate-800">
                <div class="container mx-auto px-4 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-xs text-slate-500 text-center md:text-left">
                        &copy; {{ date('Y') }} Ultimate Store. All rights reserved. Design by Laravel Team.
                    </p>
                    <div
                        class="flex items-center gap-4 grayscale opacity-70 hover:grayscale-0 hover:opacity-100 transition-all duration-300">
                        <div
                            class="h-6 w-10 bg-slate-700 rounded flex items-center justify-center text-[10px] font-bold">
                            VISA</div>
                        <div
                            class="h-6 w-10 bg-slate-700 rounded flex items-center justify-center text-[10px] font-bold">
                            MOMO</div>
                        <div
                            class="h-6 w-10 bg-slate-700 rounded flex items-center justify-center text-[10px] font-bold">
                            COD</div>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Scripts -->
        <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
        <script src="https://unpkg.com/lucide@latest"></script>
        <script src="//unpkg.com/alpinejs" defer></script>
        {{--
    </div> --}}

    {{-- JS cho Homepage Slide --}}
    @if (Route::is('home'))
        <script src="{{ asset('js/homepage.js') }}"></script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>

    {{-- In ra debug từ server nếu có --}}
    @if(session()->has('server_debug') || isset($server_debug))
        @php
            $debug = session('server_debug') ?? $server_debug;
        @endphp
        <script>
            (function () {
                try {
                    console.group("%c SERVER RESPONSE DEBUG ", "color: white; background: #f05252; font-weight: bold; padding: 2px 5px; border-radius: 3px;");
                    console.log("Module:", {!! json_encode($debug['module'] ?? 'N/A') !!});
                    console.log("Action:", {!! json_encode($debug['action'] ?? 'N/A') !!});
                    console.log("Payload Summary:", @json($debug));
                    console.groupEnd();
                } catch (e) {
                    console.warn('Server debug print failed', e);
                }
            })();
        </script>
    @endif

    {{-- Component Toast --}}
    <x-toast />

    {{-- Chat Widget --}}
    @include('partials.chat-widget')

    {{-- Script để lắng nghe Flash Message từ Session PHP --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Profile & Account Updates
            @if (session('status') === 'profile-updated')
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: 'Cập nhật hồ sơ thành công!', type: 'success' }
                }));
            @endif

            @if (session('status') === 'password-updated')
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: 'Đổi mật khẩu thành công!', type: 'success' }
                }));
            @endif

            @if (session('status') === 'avatar-updated')
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: 'Cập nhật ảnh đại diện thành công!', type: 'success' }
                }));
            @endif

            // 2. General Success Messages (e.g. Order success, Cart actions)
            @if (session('success'))
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: "{{ session('success') }}", type: 'success' }
                }));
            @endif

            // 3. General Error Messages
            @if (session('error'))
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: "{{ session('error') }}", type: 'error' }
                }));
            @endif

            // 4. Validation Errors (Optional: Show first error as toast)
            @if ($errors->any())
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: "{{ $errors->first() }}", type: 'error' }
                }));
            @endif
        });
    </script>
</body>

</html>
