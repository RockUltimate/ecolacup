{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EcolaCup') }}</title>
    <script>
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-surface antialiased dark:bg-[#1c1c19] dark:text-[#e5e2dd]">
    @include('layouts.navigation')

    @isset($header)
        <header class="border-b border-outline-variant/20 bg-surface-container-low/60 backdrop-blur-sm dark:border-[#43493e]/30 dark:bg-[#252522]/60">
            <div class="mx-auto max-w-7xl px-4 py-7 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main class="relative mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
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

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_300px]">
            <div>{{ $slot }}</div>
            <aside class="space-y-4">
                <section class="panel p-5">
                    <p class="section-eyebrow">Kalendář</p>
                    <h3 class="mt-2 font-headline text-lg text-primary dark:text-inverse-primary">Nadcházející události</h3>
                    <ul class="mt-3 space-y-2 text-sm">
                        @forelse($upcomingEvents as $event)
                            <li>
                                <a href="{{ route('udalosti.show', $event) }}" class="brand-link">{{ $event->nazev }}</a>
                                <p class="text-on-surface-variant dark:text-[#c3c8bb]">{{ $event->datum_zacatek?->format('d.m.Y') }}</p>
                            </li>
                        @empty
                            <li class="text-on-surface-variant dark:text-[#c3c8bb]">Momentálně nejsou vypsané žádné akce.</li>
                        @endforelse
                    </ul>
                </section>
                <section class="panel p-5">
                    <p class="section-eyebrow">Moje agenda</p>
                    <h3 class="mt-2 font-headline text-lg text-secondary dark:text-secondary-fixed-dim">Moje přihlášky</h3>
                    <ul class="mt-3 space-y-2 text-sm">
                        @forelse($myOpenRegistrations as $registration)
                            <li>
                                <a href="{{ route('prihlasky.show', $registration) }}" class="brand-link">{{ $registration->udalost?->nazev }}</a>
                                <p class="text-on-surface-variant dark:text-[#c3c8bb]">{{ $registration->kun?->jmeno }}</p>
                            </li>
                        @empty
                            <li class="text-on-surface-variant dark:text-[#c3c8bb]">Zatím nemáte aktivní přihlášky.</li>
                        @endforelse
                    </ul>
                </section>
            </aside>
        </div>
    </main>
</body>
</html>
