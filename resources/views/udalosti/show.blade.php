{{-- resources/views/udalosti/show.blade.php --}}
<x-site-layout>

@php
    $closed = ($udalost->uzavierka_prihlasek && $udalost->uzavierka_prihlasek->lt(now()->startOfDay()))
           || ($udalost->kapacita !== null && $udalost->pocet_prihlasek >= $udalost->kapacita);
@endphp

{{-- ── Hero ─────────────────────────────────────────────────────── --}}
<section class="relative min-h-[600px] overflow-hidden">
    <div class="mx-auto flex max-w-screen-xl items-center gap-8 px-6 py-20 lg:px-8">

        {{-- Left: event info --}}
        <div class="relative z-10 w-full lg:w-3/5">
            <span class="brand-pill mb-6 inline-block">CMT Závod</span>
            <h1 class="font-headline text-6xl leading-none text-primary dark:text-inverse-primary lg:text-8xl">
                {{ $udalost->nazev }}
            </h1>
            <p class="mt-6 max-w-lg text-xl text-on-surface-variant dark:text-[#c3c8bb]">
                {{ $udalost->misto }}
                @if($udalost->datum_zacatek)
                    • {{ $udalost->datum_zacatek->format('d.m.Y') }}
                    @if($udalost->datum_konec && $udalost->datum_konec->ne($udalost->datum_zacatek))
                        – {{ $udalost->datum_konec->format('d.m.Y') }}
                    @endif
                @endif
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
                @if(!$closed)
                    @auth
                        <a href="{{ route('prihlasky.create', $udalost) }}" class="button-primary px-10 py-4">
                            Přihlásit se
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="button-primary px-10 py-4">
                            Přihlásit se
                        </a>
                    @endauth
                @else
                    <span class="button-secondary cursor-not-allowed px-10 py-4 opacity-60">Registrace uzavřena</span>
                @endif
                <a href="{{ route('udalosti.index') }}" class="button-secondary px-10 py-4">
                    Všechny akce
                </a>
            </div>
        </div>

        {{-- Right: gradient image placeholder --}}
        <div class="absolute right-0 top-0 hidden h-full w-2/5 lg:block">
            <div class="h-full w-full overflow-hidden" style="border-radius: 2rem 0.5rem 0.5rem 5rem;">
                <div class="h-full w-full"
                     style="background: linear-gradient(135deg, #173809 0%, #2d4f1e 50%, #446733 100%); opacity: 0.85;"></div>
            </div>
        </div>
    </div>
</section>

{{-- ── Stats bento grid ─────────────────────────────────────────── --}}
<section class="px-6 py-16 lg:px-8">
    <div class="mx-auto grid max-w-screen-xl grid-cols-1 gap-6 md:grid-cols-4">

        {{-- Kapacita --}}
        <div class="rounded-xl bg-surface-container-low p-8 dark:bg-[#252522]">
            <h3 class="font-headline text-2xl text-primary dark:text-inverse-primary">Kapacita</h3>
            @if($udalost->kapacita)
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="font-headline text-5xl text-primary dark:text-inverse-primary">{{ $udalost->pocet_prihlasek }}</span>
                    <span class="text-sm uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">/ {{ $udalost->kapacita }}</span>
                </div>
                <p class="mt-2 text-sm text-on-surface-variant dark:text-[#c3c8bb]">obsazených míst</p>
            @else
                <p class="mt-4 font-headline text-3xl text-primary dark:text-inverse-primary">{{ $udalost->pocet_prihlasek }}</p>
                <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">přihlášek (bez limitu)</p>
            @endif
        </div>

        {{-- Uzávěrka --}}
        <div class="rounded-xl bg-surface-container-highest p-8 dark:bg-[#3b3b38]">
            <h3 class="font-headline text-xl text-primary dark:text-inverse-primary">Uzávěrka</h3>
            <p class="mt-4 font-headline text-3xl text-on-surface dark:text-[#e5e2dd]">
                {{ $udalost->uzavierka_prihlasek?->format('d.m.Y') ?? '—' }}
            </p>
            <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">přihlášek</p>
        </div>

        {{-- Status --}}
        <div class="flex flex-col items-center justify-center rounded-xl p-8 text-center
                    {{ $closed ? 'bg-inverse-surface text-inverse-on-surface dark:bg-[#31302d]' : 'bg-primary text-on-primary' }}">
            <p class="text-xs uppercase tracking-widest opacity-80">Stav</p>
            <p class="mt-2 font-headline text-2xl italic">{{ $closed ? 'Uzavřeno' : 'Otevřeno' }}</p>
        </div>

        {{-- Schedule --}}
        <div class="rounded-xl bg-surface-container-low p-8 dark:bg-[#252522]">
            <h3 class="mb-4 text-xs font-bold uppercase tracking-widest text-primary dark:text-inverse-primary">Termíny</h3>
            <ul class="space-y-3 text-sm">
                @if($udalost->datum_zacatek)
                    <li class="flex justify-between border-b border-outline-variant/20 pb-2 dark:border-[#43493e]/30">
                        <span class="font-semibold text-on-surface dark:text-[#e5e2dd]">Zahájení</span>
                        <span class="text-on-surface-variant dark:text-[#c3c8bb]">{{ $udalost->datum_zacatek->format('d.m.Y') }}</span>
                    </li>
                @endif
                @if($udalost->datum_konec)
                    <li class="flex justify-between border-b border-outline-variant/20 pb-2 dark:border-[#43493e]/30">
                        <span class="font-semibold text-on-surface dark:text-[#e5e2dd]">Ukončení</span>
                        <span class="text-on-surface-variant dark:text-[#c3c8bb]">{{ $udalost->datum_konec->format('d.m.Y') }}</span>
                    </li>
                @endif
                @if($udalost->uzavierka_prihlasek)
                    <li class="flex justify-between">
                        <span class="font-semibold text-on-surface dark:text-[#e5e2dd]">Uzávěrka</span>
                        <span class="text-on-surface-variant dark:text-[#c3c8bb]">{{ $udalost->uzavierka_prihlasek->format('d.m.Y') }}</span>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</section>

{{-- ── Disciplines ──────────────────────────────────────────────── --}}
@if($udalost->moznosti->isNotEmpty())
<section class="bg-surface-container-low py-20 dark:bg-[#252522]">
    <div class="mx-auto grid max-w-screen-xl gap-16 px-6 lg:grid-cols-2 lg:px-8">
        <div>
            <h2 class="font-headline text-4xl text-primary dark:text-inverse-primary">Disciplíny</h2>
            <p class="mt-4 text-on-surface-variant dark:text-[#c3c8bb]">
                Přehled kategorií a startovních poplatků pro tuto akci.
            </p>
        </div>
        <div class="space-y-0">
            @foreach($udalost->moznosti as $moznost)
                <div class="flex items-center justify-between py-4
                            {{ !$loop->last ? 'border-b border-outline-variant/20 dark:border-[#43493e]/30' : '' }}">
                    <div>
                        <p class="font-semibold text-on-surface dark:text-[#e5e2dd]">{{ $moznost->nazev }}</p>
                        @if($moznost->popis)
                            <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">{{ $moznost->popis }}</p>
                        @endif
                    </div>
                    <span class="ml-4 font-headline text-xl text-primary dark:text-inverse-primary">
                        {{ number_format($moznost->cena, 0, ',', ' ') }} Kč
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── Stabling ─────────────────────────────────────────────────── --}}
@if($udalost->ustajeniMoznosti->isNotEmpty())
<section class="py-20">
    <div class="mx-auto max-w-screen-xl px-6 lg:px-8">
        <h2 class="mb-10 font-headline text-4xl text-on-surface dark:text-[#e5e2dd]">Ustájení</h2>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($udalost->ustajeniMoznosti as $ustajeni)
                <div class="rounded-xl bg-surface-container-highest p-6 dark:bg-[#3b3b38]">
                    <h3 class="font-headline text-xl text-on-surface dark:text-[#e5e2dd]">{{ $ustajeni->nazev }}</h3>
                    @if($ustajeni->popis)
                        <p class="mt-2 text-sm text-on-surface-variant dark:text-[#c3c8bb]">{{ $ustajeni->popis }}</p>
                    @endif
                    <p class="mt-4 font-headline text-2xl text-primary dark:text-inverse-primary">
                        {{ number_format($ustajeni->cena, 0, ',', ' ') }} Kč
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── Registration CTA ─────────────────────────────────────────── --}}
@if(!$closed)
<section class="bg-surface-container-low py-20 dark:bg-[#252522]">
    <div class="mx-auto max-w-screen-xl px-6 text-center lg:px-8">
        <h2 class="font-headline text-4xl text-on-surface dark:text-[#e5e2dd]">Přihlásit se na tuto akci</h2>
        <p class="mt-4 text-on-surface-variant dark:text-[#c3c8bb]">Uzávěrka přihlášek: {{ $udalost->uzavierka_prihlasek?->format('d.m.Y') ?? 'neurčena' }}</p>
        <div class="mt-8">
            @auth
                <a href="{{ route('prihlasky.create', $udalost) }}" class="button-primary px-12 py-5 text-base">
                    Přihlásit se
                </a>
            @else
                <a href="{{ route('register') }}" class="button-primary px-12 py-5 text-base">
                    Vytvořit účet a přihlásit se
                </a>
            @endauth
        </div>
    </div>
</section>
@endif

</x-site-layout>
