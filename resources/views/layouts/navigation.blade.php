{{-- resources/views/layouts/navigation.blade.php --}}
<nav class="site-nav">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('udalosti.index') }}" class="flex items-center gap-3">
            <span class="site-mark">EC</span>
            <span class="flex flex-col">
                <span class="text-sm font-bold uppercase tracking-[0.28em] text-secondary dark:text-secondary-fixed-dim">EcolaCup</span>
                <span class="text-xs text-on-surface-variant dark:text-[#8d9387]">Czech Mountain Trail</span>
            </span>
        </a>

        <nav class="hidden items-center gap-6 text-sm md:flex">
            <a href="{{ route('udalosti.index') }}" class="brand-link">Události</a>
            <a href="{{ route('prihlasky.index') }}" class="brand-link">Přihlášky</a>
            @if(auth()->user()?->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="brand-link">Admin</a>
            @endif
        </nav>

        <div class="flex items-center gap-3">
            <a href="{{ route('ucet.edit') }}"
               class="text-sm font-semibold text-on-surface-variant transition hover:text-on-surface dark:text-[#c3c8bb] dark:hover:text-[#e5e2dd]">
                {{ auth()->user()?->name }}
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="button-secondary px-4 py-2 text-xs">
                    Odhlásit
                </button>
            </form>
        </div>
    </div>
</nav>
