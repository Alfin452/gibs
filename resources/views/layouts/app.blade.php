<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Portal GIBS') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900">

    <div class="min-h-screen flex flex-row">

        @include('layouts.sidebar')

        <main class="flex-1 md:ml-64 min-h-screen flex flex-col transition-all duration-300">

            <div class="md:hidden h-16 bg-white border-b flex items-center justify-between px-4 sticky top-0 z-40">
                <span class="font-bold text-lg text-indigo-600">PORTAL GIBS</span>
                <button class="text-gray-500 p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>

            @if (isset($header))
            <header class="bg-white shadow-sm sticky top-0 z-30">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endif

            <div class="flex-1 p-6">
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </div>

            <footer class="py-4 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} Portal GIBS. All rights reserved.
            </footer>

        </main>
    </div>

</body>

</html>