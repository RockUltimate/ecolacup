{{-- resources/views/admin/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="section-eyebrow">Administrace</p>
                <h1 class="mt-2 font-headline text-4xl italic text-primary dark:text-inverse-primary">
                    Přehled administrace
                </h1>
                <p class="mt-2 text-on-surface-variant dark:text-[#c3c8bb]">Správa událostí, přihlášek a uživatelů.</p>
            </div>
            <a href="{{ route('admin.udalosti.create') }}" class="button-primary flex items-center gap-2">
                + Vytvořit událost
            </a>
        </div>
    </x-slot>

    {{-- ── Metrics bento ──────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-6 lg:grid-cols-4">
        <div class="panel relative overflow-hidden p-8">
            <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Aktivní události</p>
            <p class="mt-3 font-headline text-4xl text-primary dark:text-inverse-primary">{{ $aktivniCount ?? 0 }}</p>
            <div class="absolute -bottom-4 -right-4 text-8xl opacity-[0.07] select-none">🏁</div>
        </div>

        <div class="panel relative overflow-hidden p-8">
            <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Přihlášky celkem</p>
            <p class="mt-3 font-headline text-4xl text-primary dark:text-inverse-primary">{{ $prihlaskyCount ?? 0 }}</p>
            <div class="absolute -bottom-4 -right-4 text-8xl opacity-[0.07] select-none">📋</div>
        </div>

        <div class="panel col-span-2 p-8" style="background: linear-gradient(135deg, #2d4f1e 0%, #173809 100%);">
            <p class="text-xs font-bold uppercase tracking-widest text-white/70">Události v systému</p>
            <p class="mt-3 font-headline text-4xl text-white">{{ $celkemUdalosti ?? 0 }}</p>
            <div class="mt-4 flex items-center gap-4">
                <div class="h-1 flex-1 overflow-hidden rounded-full bg-white/20">
                    @php $pct = ($celkemUdalosti ?? 0) > 0 ? min(100, (($aktivniCount ?? 0) / ($celkemUdalosti ?? 1)) * 100) : 0; @endphp
                    <div class="h-full rounded-full bg-white/80" style="width: {{ $pct }}%;"></div>
                </div>
                <span class="text-xs font-bold text-white/80">{{ round($pct) }}% aktivních</span>
            </div>
        </div>
    </div>

    {{-- ── Upcoming events list ────────────────────────────────── --}}
    <div class="mt-10 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-headline text-2xl italic text-on-surface dark:text-[#e5e2dd]">Nadcházející události</h2>
            <a href="{{ route('admin.udalosti.index') }}" class="text-sm font-bold uppercase tracking-widest text-primary underline underline-offset-4 dark:text-inverse-primary">
                Zobrazit vše
            </a>
        </div>

        @forelse($nadchazejici ?? [] as $udalost)
            <div class="panel flex flex-col items-stretch gap-6 p-6 transition-shadow hover:shadow-md lg:flex-row lg:items-center">
                {{-- Thumbnail --}}
                <div class="h-24 w-full flex-shrink-0 overflow-hidden rounded-xl lg:w-40"
                     style="background: linear-gradient(135deg, #173809 0%, #446733 100%);"></div>

                <div class="flex-1 min-w-0 space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="brand-pill">{{ $udalost->aktivni ? 'Aktivní' : 'Archiv' }}</span>
                        <span class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">
                            {{ $udalost->datum_zacatek?->format('d.m.Y') }}
                        </span>
                    </div>
                    <p class="font-headline text-xl text-on-surface dark:text-[#e5e2dd]">{{ $udalost->nazev }}</p>
                    <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">{{ $udalost->misto }}</p>
                </div>

                <div class="grid grid-cols-2 gap-6 border-y border-outline-variant/20 py-4 lg:border-x lg:border-y-0 lg:px-8 lg:py-0 dark:border-[#43493e]/30">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Přihlášky</p>
                        <p class="mt-1 font-headline text-xl text-on-surface dark:text-[#e5e2dd]">
                            {{ $udalost->pocet_prihlasek }}{{ $udalost->kapacita ? ' / ' . $udalost->kapacita : '' }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.udalosti.edit', $udalost) }}" class="button-secondary px-4 py-2 text-xs">Nastavení</a>
                    <a href="{{ route('admin.reports.prihlasky', $udalost) }}" class="button-primary px-4 py-2 text-xs">Přihlášky</a>
                </div>
            </div>
        @empty
            <div class="panel p-10 text-center">
                <p class="text-on-surface-variant dark:text-[#c3c8bb]">Zatím nejsou vytvořené žádné události.</p>
                <a href="{{ route('admin.udalosti.create') }}" class="button-primary mt-4 inline-flex">Vytvořit první událost</a>
            </div>
        @endforelse
    </div>

    {{-- ── Admin navigation shortcuts ──────────────────────────── --}}
    <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <a href="{{ route('admin.udalosti.index') }}"
           class="panel p-5 text-center transition-colors hover:bg-surface-container-low dark:hover:bg-[#2a2a27]">
            <p class="font-bold text-on-surface dark:text-[#e5e2dd]">Události</p>
            <p class="mt-1 text-xs text-on-surface-variant dark:text-[#c3c8bb]">Správa akcí</p>
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="panel p-5 text-center transition-colors hover:bg-surface-container-low dark:hover:bg-[#2a2a27]">
            <p class="font-bold text-on-surface dark:text-[#e5e2dd]">Uživatelé</p>
            <p class="mt-1 text-xs text-on-surface-variant dark:text-[#c3c8bb]">Správa účtů</p>
        </a>
        <a href="{{ route('admin.clenstvi.index') }}"
           class="panel p-5 text-center transition-colors hover:bg-surface-container-low dark:hover:bg-[#2a2a27]">
            <p class="font-bold text-on-surface dark:text-[#e5e2dd]">Členství CMT</p>
            <p class="mt-1 text-xs text-on-surface-variant dark:text-[#c3c8bb]">Správa členství</p>
        </a>
        <a href="{{ route('udalosti.index') }}"
           class="panel p-5 text-center transition-colors hover:bg-surface-container-low dark:hover:bg-[#2a2a27]">
            <p class="font-bold text-on-surface dark:text-[#e5e2dd]">Veřejný web</p>
            <p class="mt-1 text-xs text-on-surface-variant dark:text-[#c3c8bb]">Zobrazit jako návštěvník</p>
        </a>
    </div>

</x-app-layout>
