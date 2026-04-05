<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'EcolaCup') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="site-shell antialiased text-gray-900">
        <div class="site-chroma" aria-hidden="true"></div>
        <div class="relative min-h-screen">
            <header class="site-nav">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ route('udalosti.index') }}" class="flex items-center gap-3">
                        <span class="site-mark">EC</span>
                        <span class="flex flex-col">
                            <span class="text-sm font-semibold uppercase tracking-[0.28em] text-[#7b5230]">EcolaCup</span>
                            <span class="text-xs text-gray-500">Czech Mountain Trail registrace</span>
                        </span>
                    </a>

                    <nav class="hidden items-center gap-6 text-sm md:flex">
                        <a href="{{ route('udalosti.index') }}" class="brand-link">Události</a>
                        <a href="{{ route('gdpr') }}" class="brand-link">GDPR</a>
                        @auth
                            <a href="{{ route('prihlasky.index') }}" class="brand-link">Moje přihlášky</a>
                            <a href="{{ route('dashboard') }}" class="brand-link">Aplikace</a>
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="brand-link">Admin</a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="brand-link">Přihlášení</a>
                        @endauth
                    </nav>

                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="button-secondary hidden sm:inline-flex">Otevřít aplikaci</a>
                        @else
                            <a href="{{ route('register') }}" class="button-primary">Vytvořit účet</a>
                        @endauth
                    </div>
                </div>
            </header>

            @isset($header)
                <section class="border-b border-white/60 bg-white/45 backdrop-blur-sm">
                    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </section>
            @endisset

            <main>
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <x-flash-message />
                </div>

                {{ $slot }}
            </main>

            <footer class="mt-16 border-t border-[#e6dac7] bg-white/70 backdrop-blur-sm">
                <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)] lg:px-8">
                    <div class="space-y-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#7b5230]">EcolaCup</p>
                        <h2 class="max-w-xl text-2xl font-semibold text-[#20392c]">Přehled událostí, registrace jezdců a koní, exporty pro pořadatele.</h2>
                        <p class="max-w-2xl text-sm leading-6 text-gray-600">Nové rozhraní drží pohromadě veřejné přihlášky i provozní administraci tak, aby fungovalo stejně dobře na mobilu v areálu i na notebooku při přípravě závodů.</p>
                    </div>
                    <div class="grid gap-3 text-sm text-gray-600 sm:grid-cols-2">
                        <div class="space-y-2">
                            <p class="font-semibold text-[#20392c]">Rychlé odkazy</p>
                            <a href="{{ route('udalosti.index') }}" class="brand-link block">Kalendář událostí</a>
                            <a href="{{ route('gdpr') }}" class="brand-link block">Ochrana osobních údajů</a>
                        </div>
                        <div class="space-y-2">
                            <p class="font-semibold text-[#20392c]">Účet</p>
                            @auth
                                <a href="{{ route('prihlasky.index') }}" class="brand-link block">Moje přihlášky</a>
                                <a href="{{ route('ucet.edit') }}" class="brand-link block">Můj účet</a>
                            @else
                                <a href="{{ route('login') }}" class="brand-link block">Přihlášení</a>
                                <a href="{{ route('register') }}" class="brand-link block">Registrace</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
