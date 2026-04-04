<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="min-h-screen">
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
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <x-flash-message />
                @php
                    $upcomingEvents = \App\Models\Udalost::query()
                        ->where('aktivni', true)
                        ->whereDate('datum_zacatek', '>=', now()->startOfDay())
                        ->orderBy('datum_zacatek')
                        ->limit(3)
                        ->get();
                    $myOpenRegistrations = auth()->check()
                        ? auth()->user()->prihlasky()->with(['udalost', 'kun'])->where('smazana', false)->latest()->limit(4)->get()
                        : collect();
                @endphp
                <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_300px] gap-6">
                    <div>
                        {{ $slot }}
                    </div>
                    <aside class="space-y-4">
                        <section class="panel p-4">
                            <h3 class="text-lg font-semibold text-[#3d6b4f]">Nadcházející události</h3>
                            <ul class="mt-3 space-y-2 text-sm">
                                @forelse($upcomingEvents as $event)
                                    <li>
                                        <a class="brand-link" href="{{ route('udalosti.show', $event) }}">{{ $event->nazev }}</a>
                                        <p class="text-gray-600">{{ $event->datum_zacatek?->format('d.m.Y') }}</p>
                                    </li>
                                @empty
                                    <li class="text-gray-600">Momentálně nejsou vypsané žádné akce.</li>
                                @endforelse
                            </ul>
                        </section>
                        <section class="panel p-4">
                            <h3 class="text-lg font-semibold text-[#7b5230]">Moje otevřené přihlášky</h3>
                            <ul class="mt-3 space-y-2 text-sm">
                                @forelse($myOpenRegistrations as $registration)
                                    <li>
                                        <a class="brand-link" href="{{ route('prihlasky.show', $registration) }}">
                                            {{ $registration->udalost?->nazev }}
                                        </a>
                                        <p class="text-gray-600">{{ $registration->kun?->jmeno }}</p>
                                    </li>
                                @empty
                                    <li class="text-gray-600">Zatím nemáte aktivní přihlášky.</li>
                                @endforelse
                            </ul>
                        </section>
                    </aside>
                </div>
            </main>
        </div>
    </body>
</html>
