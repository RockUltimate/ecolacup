{{-- resources/views/prihlasky/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="section-eyebrow">Moje záznamy</p>
                <h1 class="mt-2 font-headline text-4xl italic text-primary dark:text-inverse-primary">Moje přihlášky</h1>
            </div>
            <a href="{{ route('udalosti.index') }}" class="button-primary">
                + Nová přihláška
            </a>
        </div>
    </x-slot>

    {{-- ── Metrics bento ──────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-6 lg:grid-cols-4">
        @php
            $active = $prihlasky->where('smazana', false)
                ->filter(fn($p) => $p->udalost?->datum_konec?->gte(now()) ?? false)->count();
            $upcoming = $prihlasky->where('smazana', false)
                ->filter(fn($p) => $p->udalost?->datum_zacatek?->gt(now()) ?? false)->count();
            $nextEntry = $prihlasky->where('smazana', false)
                ->filter(fn($p) => $p->udalost?->datum_zacatek?->gt(now()) ?? false)
                ->sortBy(fn($p) => $p->udalost?->datum_zacatek)
                ->first();
        @endphp

        <div class="panel relative overflow-hidden p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Aktivní přihlášky</p>
            <p class="mt-3 font-headline text-4xl text-primary dark:text-inverse-primary">{{ $active }}</p>
        </div>

        <div class="panel relative overflow-hidden p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Nadcházející</p>
            <p class="mt-3 font-headline text-4xl text-primary dark:text-inverse-primary">{{ $upcoming }}</p>
        </div>

        <div class="panel col-span-2 p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Příští akce</p>
            @if($nextEntry)
                <p class="mt-3 font-headline text-2xl text-on-surface dark:text-[#e5e2dd]">{{ $nextEntry->udalost?->nazev }}</p>
                <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">
                    {{ $nextEntry->udalost?->datum_zacatek?->format('d.m.Y') }}
                    • {{ $nextEntry->kun?->jmeno }}
                </p>
            @else
                <p class="mt-3 text-on-surface-variant dark:text-[#c3c8bb]">Žádná nadcházející akce.</p>
            @endif
        </div>
    </div>

    {{-- ── Registration list ──────────────────────────────────── --}}
    <div class="mt-8 space-y-4">
        <h2 class="font-headline text-2xl italic text-on-surface dark:text-[#e5e2dd]">Všechny přihlášky</h2>

        @forelse($prihlasky->where('smazana', false) as $prihlaska)
            @php
                $eventPast = $prihlaska->udalost?->datum_konec?->lt(now()) ?? false;
            @endphp
            <div class="panel flex flex-col gap-5 p-6 transition-shadow hover:shadow-md sm:flex-row sm:items-center">
                {{-- Gradient thumbnail --}}
                <div class="h-20 w-24 flex-shrink-0 overflow-hidden rounded-xl"
                     style="background: linear-gradient(135deg, #173809 0%, #2d4f1e 100%);"></div>

                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="brand-pill">{{ $eventPast ? 'Absolvováno' : 'Aktivní' }}</span>
                        @if($prihlaska->moznosti->isNotEmpty())
                            @foreach($prihlaska->moznosti->take(2) as $moznost)
                                <span class="rounded-full bg-surface-container-high px-2 py-0.5 text-xs font-semibold text-on-surface dark:bg-[#313130] dark:text-[#e5e2dd]">
                                    {{ $moznost->nazev }}
                                </span>
                            @endforeach
                        @endif
                    </div>
                    <p class="font-semibold text-on-surface dark:text-[#e5e2dd]">{{ $prihlaska->udalost?->nazev }}</p>
                    <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">
                        {{ $prihlaska->udalost?->datum_zacatek?->format('d.m.Y') }}
                        @if($prihlaska->kun) • {{ $prihlaska->kun->jmeno }} @endif
                        @if($prihlaska->osoba) • {{ $prihlaska->osoba->jmeno_prijmeni }} @endif
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('prihlasky.show', $prihlaska) }}" class="button-secondary px-4 py-2 text-xs">Detail</a>
                    @if(!$eventPast)
                        <a href="{{ route('prihlasky.edit', $prihlaska) }}" class="button-secondary px-4 py-2 text-xs">Upravit</a>
                    @endif
                    <a href="{{ route('prihlasky.pdf', $prihlaska) }}" class="button-secondary px-4 py-2 text-xs">PDF</a>
                </div>
            </div>
        @empty
            <div class="panel p-10 text-center">
                <p class="font-headline text-xl text-on-surface-variant dark:text-[#c3c8bb]">Zatím žádné přihlášky.</p>
                <a href="{{ route('udalosti.index') }}" class="button-primary mt-6 inline-flex">Prohlédnout akce</a>
            </div>
        @endforelse
    </div>

    {{-- ── Quick links ─────────────────────────────────────────── --}}
    <div class="mt-8 grid grid-cols-3 gap-4">
        <a href="{{ route('kone.index') }}"
           class="panel flex flex-col items-center gap-2 p-5 text-center transition-colors hover:bg-surface-container-low dark:hover:bg-[#2a2a27]">
            <span class="font-headline text-2xl text-primary dark:text-inverse-primary">🐎</span>
            <span class="text-sm font-semibold text-on-surface dark:text-[#e5e2dd]">Moje koně</span>
        </a>
        <a href="{{ route('osoby.index') }}"
           class="panel flex flex-col items-center gap-2 p-5 text-center transition-colors hover:bg-surface-container-low dark:hover:bg-[#2a2a27]">
            <span class="font-headline text-2xl text-primary dark:text-inverse-primary">👤</span>
            <span class="text-sm font-semibold text-on-surface dark:text-[#e5e2dd]">Moje osoby</span>
        </a>
        <a href="{{ route('clenstvi-cmt.index') }}"
           class="panel flex flex-col items-center gap-2 p-5 text-center transition-colors hover:bg-surface-container-low dark:hover:bg-[#2a2a27]">
            <span class="font-headline text-2xl text-primary dark:text-inverse-primary">🏆</span>
            <span class="text-sm font-semibold text-on-surface dark:text-[#e5e2dd]">CMT členství</span>
        </a>
    </div>

</x-app-layout>
