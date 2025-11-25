<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ expanded: localStorage.getItem('sidebarExpanded') !== 'false', toggleSidebar() { this.expanded = !this.expanded; localStorage.setItem('sidebarExpanded', this.expanded); } }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Admin System</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-900">

    <div class="flex h-screen overflow-hidden">

        {{-- Include Sidebar --}}
        @include('layouts.admin-navigation')

        {{-- Main Content --}}
        <div class="flex flex-1 flex-col overflow-hidden transition-all duration-300 ease-in-out"
            :class="expanded ? 'ml-64' : 'ml-20'">

            <header class="bg-white shadow-sm border-b border-gray-200 z-10">
                <div class="flex justify-between items-center h-16 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button @click="toggleSidebar()" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">@yield('header')</h1>
                    </div>

                    <div class="flex items-center">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ml-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg></div>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50"
                                style="display: none;">
                                <a href="{{ route('home') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Về trang khách</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @if(session('status'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border-l-4 border-green-500 shadow-sm"
                        role="alert">
                        <p class="font-medium">Thành công!</p>
                        <p>{{ session('status') }}</p>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- REQUIRED: SERVER RESPONSE DEBUG SCRIPT --}}
    @if(session()->has('server_debug') || isset($server_debug))
        @php $debug = session('server_debug') ?? $server_debug; @endphp
        <script>
            (function () {
                try {
                    console.group("%c SERVER RESPONSE DEBUG ", "color: white; background: #f05252; font-weight: bold; padding: 2px 5px; border-radius: 3px;");
                    console.log("Module:", {!! json_encode($debug['module'] ?? 'N/A') !!});
                    console.log("Action:", {!! json_encode($debug['action'] ?? 'N/A') !!});
                    console.log("Payload Summary:", @json($debug));
                    console.groupEnd();
                } catch (e) { console.warn('Debug print failed', e); }
            })();
        </script>
    @endif

    @stack('scripts')
</body>

</html>
