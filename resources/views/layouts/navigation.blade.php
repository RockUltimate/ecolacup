<nav x-data="{ open: false }" class="site-nav">
    <div class="mx-auto flex max-w-7xl items-start justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex flex-col items-start gap-2">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('logo.png') }}" alt="Ecola Equestrian" class="h-11 w-auto">
                <span class="flex flex-col">
                    <span class="text-sm font-semibold tracking-[0.08em] text-[#7b5230]">ECOLAKONĚ</span>
                    <span class="text-xs text-gray-500">provozní přehled a registrace</span>
                </span>
            </a>

            <button @click="open = !open" class="site-nav-toggle inline-flex items-center justify-center rounded-full border border-[#e3d7c4] bg-white/75 p-2 text-[#7b5230] md:hidden">
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="hidden items-center gap-5 text-sm md:flex">
                <a href="{{ route('udalosti.index') }}" class="brand-link {{ request()->routeIs('udalosti.*') ? 'font-semibold' : '' }}">Události</a>
                <a href="{{ route('osoby.index') }}" class="brand-link {{ request()->routeIs('osoby.*') ? 'font-semibold' : '' }}">Osoby</a>
                <a href="{{ route('kone.index') }}" class="brand-link {{ request()->routeIs('kone.*') ? 'font-semibold' : '' }}">Koně</a>
                <a href="{{ route('prihlasky.index') }}" class="brand-link {{ request()->routeIs('prihlasky.*') ? 'font-semibold' : '' }}">Přihlášky</a>
                @if(Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="brand-link {{ request()->routeIs('admin.*') ? 'font-semibold' : '' }}">Admin</a>
                @endif
        </div>

        <div class="site-nav-actions flex items-center gap-3">
            <a href="{{ route('ucet.edit') }}" class="button-secondary hidden sm:inline-flex">{{ Auth::user()->celeJmeno() }}</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="button-primary">Odhlásit</button>
            </form>
        </div>
    </div>

    <div x-cloak x-show="open" x-transition.opacity class="border-t border-white/60 bg-[#fffaf2]/95 px-4 py-4 md:hidden">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 text-sm text-gray-700">
            <a href="{{ route('udalosti.index') }}" class="brand-link">Události</a>
            <a href="{{ route('osoby.index') }}" class="brand-link">Osoby</a>
            <a href="{{ route('kone.index') }}" class="brand-link">Koně</a>
            <a href="{{ route('prihlasky.index') }}" class="brand-link">Přihlášky</a>
            @if(Auth::user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="brand-link">Admin</a>
            @endif
            <a href="{{ route('ucet.edit') }}" class="brand-link">Můj účet</a>
        </div>
    </div>
</nav>
