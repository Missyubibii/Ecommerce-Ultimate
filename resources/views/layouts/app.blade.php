<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
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
            <main>
                {{ $slot }}
            </main>
        </div>
        @if(session()->has('server_debug') || isset($server_debug))
            @php
                $debug = session('server_debug') ?? $server_debug;
            @endphp
            <script>
                (function(){
                    try {
                        console.group("%c SERVER RESPONSE DEBUG ", "color: white; background: #f05252; font-weight: bold; padding: 2px 5px; border-radius: 3px;");
                        console.log("Module:", {!! json_encode($debug['module'] ?? 'N/A') !!});
                        console.log("Action:", {!! json_encode($debug['action'] ?? 'N/A') !!});
                        console.log("Payload Summary:", @json($debug));
                        console.groupEnd();
                    } catch(e) {
                        console.warn('Server debug print failed', e);
                    }
                })();
            </script>
        @endif
    </body>
</html>
