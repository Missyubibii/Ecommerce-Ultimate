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
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Animation for Marquee -->
    <style>
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
        <footer class="bg-gray-900 text-gray-300 py-10 mt-auto">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-white font-bold text-lg mb-4">Về chúng tôi</h3>
                        <p class="text-sm">Chuyên cung cấp máy tính, linh kiện điện tử chính hãng với giá tốt nhất thị
                            trường.</p>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg mb-4">Chính sách</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-white">Bảo hành</a></li>
                            <li><a href="#" class="hover:text-white">Đổi trả</a></li>
                            <li><a href="#" class="hover:text-white">Vận chuyển</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg mb-4">Liên hệ</h3>
                        <ul class="space-y-2 text-sm">
                            <li>Hotline: 1900 1000</li>
                            <li>Email: support@example.com</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg mb-4">Theo dõi</h3>
                        <div class="flex space-x-4">
                            <!-- Social Icons placeholder -->
                            <a href="#"
                                class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-indigo-600 transition"><i
                                    data-lucide="facebook" class="w-4 h-4"></i></a>
                            <a href="#"
                                class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-pink-600 transition"><i
                                    data-lucide="instagram" class="w-4 h-4"></i></a>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
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
