<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Admin System</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Tùy chỉnh thanh cuộn cho đẹp hơn */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c7c7c7;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-900" x-data="{
        expanded: localStorage.getItem('sidebarExpanded') !== 'false',
        toggleSidebar() {
            this.expanded = !this.expanded;
            localStorage.setItem('sidebarExpanded', this.expanded);
        }
    }">

    <div class="flex h-screen overflow-hidden">

        {{-- Include Sidebar --}}
        @include('layouts.admin-navigation')

        {{-- Main Content Wrapper --}}
        <div class="flex flex-1 flex-col overflow-hidden transition-all duration-300 ease-in-out"
            :class="expanded ? 'ml-64' : 'ml-20'">

            {{-- Top Header --}}
            <header class="bg-white shadow-sm border-b border-gray-200 z-10 sticky top-0">
                <div class="flex justify-between items-center h-16 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button @click="toggleSidebar()"
                            class="text-gray-500 hover:text-indigo-600 focus:outline-none transition-colors">
                            <i data-lucide="menu" class="w-6 h-6"></i>
                        </button>
                        <h1 class="text-xl font-bold text-gray-800 tracking-tight">@yield('header')</h1>
                    </div>

                    <div class="flex items-center gap-4">
                        {{-- Nút về trang chủ --}}
                        <a href="/" target="_blank" class="text-gray-400 hover:text-indigo-600 transition-colors"
                            title="Xem trang web">
                            <i data-lucide="globe" class="w-5 h-5"></i>
                        </a>

                        {{-- User Dropdown --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                                <div
                                    class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="hidden md:block">{{ Auth::user()->name }}</span>
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50 origin-top-right">

                                <a href="{{ route('profile.edit') }}"
                                    class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                    <i data-lucide="user" class="w-4 h-4"></i> Hồ sơ
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                        <i data-lucide="log-out" class="w-4 h-4"></i> Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Main Content Body --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                {{-- Flash Messages --}}
                @if(session('status'))
                    <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-lg border border-green-200 shadow-sm flex items-center gap-2"
                        role="alert">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        <div>
                            <span class="font-bold">Thông báo:</span> {{ session('status') }}
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- Component Toast --}}
    <x-toast />

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

    {{-- CKEditor 5 (Optional - Bỏ comment nếu cần dùng soạn thảo văn bản) --}}
    {{--
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script> --}}

    <script>
        // Khởi tạo Icons
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // Xử lý thông báo Toast
        document.addEventListener('DOMContentLoaded', () => {
            @if (session('success'))
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: "{{ session('success') }}", type: 'success' } }));
            @endif
            @if (session('error'))
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: "{{ session('error') }}", type: 'error' } }));
            @endif
            @if ($errors->any())
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: "{{ $errors->first() }}", type: 'error' } }));
            @endif
        });
    </script>

    {{-- Server Debug Script --}}
    @if(session()->has('server_debug') || isset($server_debug))
        @php $debug = session('server_debug') ?? $server_debug; @endphp
        <script>
            (function () {
                try {
                    console.group("%c ADMIN DEBUG ", "color: white; background: #4f46e5; font-weight: bold; padding: 2px 5px; border-radius: 3px;");
                    console.log("Module:", {!! json_encode($debug['module'] ?? 'N/A') !!});
                    console.log("Payload:", @json($debug));
                    console.groupEnd();
                } catch (e) { }
            })();
        </script>
    @endif

    @stack('scripts')
</body>

</html>
