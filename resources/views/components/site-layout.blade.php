<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ECOLAKONĚ') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="site-shell antialiased text-gray-900">
        @php
            $publicLinks = [
                ['label' => 'Události', 'route' => 'udalosti.index', 'active' => 'udalosti.*'],
                ['label' => 'GDPR', 'route' => 'gdpr', 'active' => 'gdpr'],
            ];

            $authenticatedLinks = [
                ['label' => 'Osoby', 'route' => 'osoby.index', 'active' => 'osoby.*'],
                ['label' => 'Koně', 'route' => 'kone.index', 'active' => 'kone.*'],
                ['label' => 'Přihlášky', 'route' => 'prihlasky.index', 'active' => 'prihlasky.*'],
            ];
        @endphp
        <div class="site-chroma" aria-hidden="true"></div>
        <div class="relative min-h-screen">
            <header x-data="{ open: false }" class="site-nav">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ route('udalosti.index') }}" class="flex items-center gap-3">
                        <span class="site-mark">EC</span>
                        <span class="flex flex-col">
                            <span class="text-sm font-semibold tracking-[0.08em] text-[#7b5230]">ECOLAKONĚ</span>
                            <span class="text-xs text-gray-500">Registrace na koňské závody</span>
                        </span>
                    </a>

                    <nav class="hidden items-center gap-6 text-sm md:flex">
                        @foreach($publicLinks as $link)
                            <a href="{{ route($link['route']) }}" class="brand-link {{ request()->routeIs($link['active']) ? 'font-semibold' : '' }}">{{ $link['label'] }}</a>
                        @endforeach
                        @auth
                            @foreach($authenticatedLinks as $link)
                                <a href="{{ route($link['route']) }}" class="brand-link {{ request()->routeIs($link['active']) ? 'font-semibold' : '' }}">{{ $link['label'] }}</a>
                            @endforeach
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="brand-link {{ request()->routeIs('admin.*') ? 'font-semibold' : '' }}">Admin</a>
                            @endif
                        @endauth
                    </nav>

                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('ucet.edit') }}" class="button-secondary hidden sm:inline-flex">{{ auth()->user()->celeJmeno() }}</a>
                            <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                                @csrf
                                <button type="submit" class="button-primary">Odhlásit</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="button-secondary">Přihlášení</a>
                            <a href="{{ route('register') }}" class="button-primary">Vytvořit účet</a>
                        @endauth

                        <button @click="open = !open" class="inline-flex items-center justify-center rounded-full border border-[#e3d7c4] bg-white/75 p-2 text-[#7b5230] md:hidden">
                            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div x-cloak x-show="open" x-transition.opacity class="border-t border-white/60 bg-[#fffaf2]/95 px-4 py-4 md:hidden">
                    <div class="mx-auto flex max-w-7xl flex-col gap-3 text-sm text-gray-700">
                        @foreach($publicLinks as $link)
                            <a href="{{ route($link['route']) }}" class="brand-link">{{ $link['label'] }}</a>
                        @endforeach

                        @auth
                            @foreach($authenticatedLinks as $link)
                                <a href="{{ route($link['route']) }}" class="brand-link">{{ $link['label'] }}</a>
                            @endforeach
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="brand-link">Admin</a>
                            @endif
                            <a href="{{ route('ucet.edit') }}" class="brand-link">Můj účet</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="button-primary mt-2 w-full">Odhlásit</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="button-secondary w-full justify-center">Přihlášení</a>
                            <a href="{{ route('register') }}" class="button-primary w-full justify-center">Vytvořit účet</a>
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
                        <p class="text-xs font-semibold tracking-[0.08em] text-[#7b5230]">ECOLAKONĚ</p>
                        <h2 class="max-w-xl text-2xl font-semibold text-[#20392c]">Přehled událostí, registrace jezdců a koní, exporty pro pořadatele.</h2>
                        <p class="max-w-2xl text-sm leading-6 text-gray-600">Nové rozhraní drží pohromadě veřejné přihlášky i provozní administraci tak, aby fungovalo stejně dobře na mobilu v areálu i na notebooku při přípravě závodů.</p>
                    </div>
                    <div class="grid gap-3 text-sm text-gray-600 sm:grid-cols-2">
                        <div class="space-y-2">
                            <p class="font-semibold text-[#20392c]">Rychlé odkazy</p>
                            @foreach($publicLinks as $link)
                                <a href="{{ route($link['route']) }}" class="brand-link block">{{ $link['label'] === 'Události' ? 'Kalendář událostí' : 'Ochrana osobních údajů' }}</a>
                            @endforeach
                        </div>
                        <div class="space-y-2">
                            <p class="font-semibold text-[#20392c]">Účet</p>
                            @auth
                                @foreach($authenticatedLinks as $link)
                                    <a href="{{ route($link['route']) }}" class="brand-link block">{{ $link['label'] }}</a>
                                @endforeach
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
