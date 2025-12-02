<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Ultimate E-Commerce') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 p-4">

        <div class="mb-6">
            <a href="/" class="flex items-center gap-2 group">
                <div class="bg-indigo-600 text-white p-2 rounded-xl shadow-lg shadow-indigo-200 group-hover:scale-110 transition-transform duration-300">
                    <i data-lucide="cpu" class="w-8 h-8"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-2xl font-extrabold text-gray-900 leading-none tracking-tight">ULTIMATE</span>
                    <span class="text-xs font-bold text-indigo-600 tracking-widest uppercase">Technology</span>
                </div>
            </a>
        </div>

        <div class="w-full sm:max-w-md bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            @yield('content')
        </div>

        <div class="mt-8 text-center text-sm text-gray-400">
            &copy; {{ date('Y') }} Ultimate Store. All rights reserved.
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
