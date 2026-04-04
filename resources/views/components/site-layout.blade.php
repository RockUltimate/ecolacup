<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-900">
        <div class="min-h-screen">
            <header class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <a href="{{ route('udalosti.index') }}" class="font-semibold text-gray-900">EcolaCup</a>
                    <nav class="flex items-center gap-4 text-sm">
                        <a href="{{ route('udalosti.index') }}" class="text-gray-700 hover:text-gray-900">Události</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900">Aplikace</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Přihlášení</a>
                            <a href="{{ route('register') }}" class="text-gray-700 hover:text-gray-900">Registrace</a>
                        @endauth
                    </nav>
                </div>
            </header>

            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
