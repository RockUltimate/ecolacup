<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased text-gray-900">
        <div class="min-h-screen">
            <header class="bg-white/90 backdrop-blur border-b border-[#e8dfd0]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <a href="{{ route('udalosti.index') }}" class="font-semibold text-[#7b5230] tracking-wide">EcolaCup</a>
                    <nav class="flex items-center gap-4 text-sm">
                        <a href="{{ route('udalosti.index') }}" class="brand-link">Události</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="brand-link">Aplikace</a>
                        @else
                            <a href="{{ route('login') }}" class="brand-link">Přihlášení</a>
                            <a href="{{ route('register') }}" class="brand-link">Registrace</a>
                        @endauth
                    </nav>
                </div>
            </header>

            @isset($header)
                <header class="bg-white/80 border-b border-[#ece2d4]">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
