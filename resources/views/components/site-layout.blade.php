{{-- resources/views/components/site-layout.blade.php --}}
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
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-surface antialiased dark:bg-[#1c1c19] dark:text-[#e5e2dd]">

    {{-- Fixed glassmorphic nav --}}
    <nav class="site-nav">
        <div class="mx-auto flex max-w-screen-xl items-center justify-between gap-6 px-6 py-4 lg:px-8">
            {{-- Logo --}}
            <a href="{{ route('udalosti.index') }}" class="font-headline text-2xl italic text-emerald-950 dark:text-emerald-100">
                EcolaCup
            </a>

            {{-- Centre links --}}
            <div class="hidden items-center gap-8 md:flex">
                <a href="{{ route('udalosti.index') }}"
                   class="text-sm font-semibold tracking-wide transition-colors
                          {{ request()->routeIs('udalosti.*') ? 'border-b-2 border-primary pb-1 text-primary dark:border-inverse-primary dark:text-inverse-primary' : 'text-on-surface-variant hover:text-on-surface dark:text-[#c3c8bb] dark:hover:text-[#e5e2dd]' }}">
                    Události
                </a>
                <a href="{{ route('gdpr') }}"
                   class="text-sm font-semibold tracking-wide text-on-surface-variant transition-colors hover:text-on-surface dark:text-[#c3c8bb] dark:hover:text-[#e5e2dd]">
                    GDPR
                </a>
                @auth
                    <a href="{{ route('prihlasky.index') }}"
                       class="text-sm font-semibold tracking-wide transition-colors
                              {{ request()->routeIs('prihlasky.*') ? 'border-b-2 border-primary pb-1 text-primary dark:border-inverse-primary dark:text-inverse-primary' : 'text-on-surface-variant hover:text-on-surface dark:text-[#c3c8bb] dark:hover:text-[#e5e2dd]' }}">
                        Moje přihlášky
                    </a>
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}"
                           class="text-sm font-semibold tracking-wide text-on-surface-variant transition-colors hover:text-on-surface dark:text-[#c3c8bb] dark:hover:text-[#e5e2dd]">
                            Admin
                        </a>
                    @endif
                @endauth
            </div>

            {{-- Right CTA --}}
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="button-primary hidden sm:inline-flex">
                        Otevřít aplikaci
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-on-surface-variant hover:opacity-80 dark:text-[#c3c8bb]">
                        Přihlásit se
                    </a>
                    <a href="{{ route('register') }}" class="button-primary">
                        Vytvořit účet
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Page content pushed below fixed nav --}}
    <div class="pt-[72px]">
        <x-flash-message />
        {{ $slot }}
    </div>

    {{-- Footer --}}
    <footer class="mt-16 rounded-t-3xl bg-stone-100 dark:bg-stone-950">
        <div class="mx-auto grid max-w-screen-xl gap-12 px-6 py-16 md:grid-cols-3 lg:px-8">
            {{-- Brand --}}
            <div class="space-y-4">
                <p class="font-headline text-xl italic text-emerald-900 dark:text-emerald-100">EcolaCup</p>
                <p class="text-sm leading-relaxed text-on-surface-variant dark:text-[#c3c8bb]">
                    Přehled gebeurteností, registrace jezdců a koní, exporty pro pořadatele Czech Mountain Trail závodů.
                </p>
                <div class="flex gap-4">
                    <span class="material-symbols-outlined cursor-pointer text-on-surface-variant transition-colors hover:text-primary dark:text-[#8d9387] dark:hover:text-[#a9d293]">public</span>
                    <span class="material-symbols-outlined cursor-pointer text-on-surface-variant transition-colors hover:text-primary dark:text-[#8d9387] dark:hover:text-[#a9d293]">mail</span>
                </div>
            </div>

            {{-- Links --}}
            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-3">
                    <p class="text-sm font-bold text-on-surface dark:text-[#e5e2dd]">Rychlé odkazy</p>
                    <a href="{{ route('udalosti.index') }}" class="block text-xs uppercase tracking-widest text-on-surface-variant transition-colors hover:text-primary dark:text-[#8d9387] dark:hover:text-[#a9d293]">Kalendář událostí</a>
                    <a href="{{ route('gdpr') }}" class="block text-xs uppercase tracking-widest text-on-surface-variant transition-colors hover:text-primary dark:text-[#8d9387] dark:hover:text-[#a9d293]">GDPR</a>
                </div>
                <div class="space-y-3">
                    <p class="text-sm font-bold text-on-surface dark:text-[#e5e2dd]">Účet</p>
                    @auth
                        <a href="{{ route('prihlasky.index') }}" class="block text-xs uppercase tracking-widest text-on-surface-variant transition-colors hover:text-primary dark:text-[#8d9387] dark:hover:text-[#a9d293]">Moje přihlášky</a>
                        <a href="{{ route('ucet.edit') }}" class="block text-xs uppercase tracking-widest text-on-surface-variant transition-colors hover:text-primary dark:text-[#8d9387] dark:hover:text-[#a9d293]">Můj účet</a>
                    @else
                        <a href="{{ route('login') }}" class="block text-xs uppercase tracking-widest text-on-surface-variant transition-colors hover:text-primary dark:text-[#8d9387] dark:hover:text-[#a9d293]">Přihlášení</a>
                        <a href="{{ route('register') }}" class="block text-xs uppercase tracking-widest text-on-surface-variant transition-colors hover:text-primary dark:text-[#8d9387] dark:hover:text-[#a9d293]">Registrace</a>
                    @endauth
                </div>
            </div>

            {{-- Newsletter (UI only — no backend) --}}
            <div class="space-y-4">
                <p class="section-eyebrow">Novinky</p>
                <p class="text-xs text-on-surface-variant dark:text-[#c3c8bb]">Dostávejte oznámení o nových závodech a termínech.</p>
                <div class="flex">
                    <input type="email" placeholder="váš@email.cz"
                           class="field-shell w-full rounded-none border-b-2 border-outline-variant bg-transparent text-xs focus:border-primary dark:border-[#43493e] dark:focus:border-[#a9d293]">
                    <button class="button-primary rounded-l-none px-4 py-2 text-xs" disabled>
                        <span class="material-symbols-outlined text-sm">send</span>
                    </button>
                </div>
                <p class="text-[10px] uppercase tracking-widest text-on-surface-variant dark:text-[#8d9387]">
                    © {{ date('Y') }} EcolaCup
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
