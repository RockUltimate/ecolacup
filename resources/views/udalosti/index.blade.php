{{-- resources/views/udalosti/index.blade.php --}}
<x-site-layout>

{{-- ── Hero ─────────────────────────────────────────────────────── --}}
<section class="relative min-h-[600px] overflow-hidden">
    <div class="mx-auto grid max-w-screen-xl items-center gap-8 px-6 py-20 lg:grid-cols-12 lg:px-8">

        {{-- Left: headline --}}
        <div class="relative z-10 lg:col-span-6">
            <p class="section-eyebrow mb-4">Kalendář akcí</p>
            <h1 class="font-headline text-5xl leading-tight text-primary dark:text-inverse-primary sm:text-6xl lg:text-7xl">
                Moderní přihlášky<br><span class="italic">na CMT závody.</span>
            </h1>
            <p class="mt-6 max-w-lg text-lg leading-relaxed text-on-surface-variant dark:text-[#c3c8bb]">
                Veřejný kalendář, přehled uzávěrek, disciplín a kapacit. Přihlášení jezdci navazují rovnou na správu osob, koní a přihlášek.
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="button-primary px-10 py-4">Pokračovat do aplikace</a>
                @else
                    <a href="{{ route('register') }}" class="button-primary px-10 py-4">Začít registraci</a>
                    <a href="{{ route('login') }}" class="button-secondary px-10 py-4">Mám účet</a>
                @endauth
            </div>
        </div>

        {{-- Right: stats / next event card --}}
        <div class="lg:col-span-6">
            @if($upcoming->isNotEmpty())
                @php $featured = $upcoming->first(); @endphp
                <div class="panel reveal-up-delay overflow-hidden">
                    {{-- Gradient hero image placeholder --}}
                    <div class="h-48 w-full"
                         style="background: linear-gradient(135deg, #173809 0%, #2d4f1e 40%, #446733 100%);">
                        <div class="flex h-full items-end p-6">
                            <span class="brand-pill">Nejbližší akce</span>
                        </div>
                    </div>
                    <div class="p-8">
                        <h2 class="font-headline text-3xl text-on-surface dark:text-[#e5e2dd]">{{ $featured->nazev }}</h2>
                        <p class="mt-2 text-on-surface-variant dark:text-[#c3c8bb]">{{ $featured->misto }} • {{ $featured->datum_zacatek?->format('d.m.Y') }}</p>
                        <a href="{{ route('udalosti.show', $featured) }}" class="button-primary mt-6 inline-flex">
                            Zobrazit detail
                        </a>
                    </div>
                </div>
            @else
                <div class="panel reveal-up-delay p-12 text-center">
                    <p class="font-headline text-2xl italic text-on-surface-variant dark:text-[#c3c8bb]">Brzy budou vypsány nové akce.</p>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ── Stats bar ────────────────────────────────────────────────── --}}
<section class="bg-surface-container-low py-10 dark:bg-[#252522]">
    <div class="mx-auto grid max-w-screen-xl grid-cols-3 gap-6 px-6 text-center lg:px-8">
        <div>
            <p class="font-headline text-4xl text-primary dark:text-inverse-primary">{{ $upcoming->count() }}</p>
            <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">nadcházejících událostí</p>
        </div>
        <div>
            <p class="font-headline text-4xl text-primary dark:text-inverse-primary">{{ $openEvents }}</p>
            <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">otevřených registrací</p>
        </div>
        <div>
            <p class="font-headline text-4xl text-primary dark:text-inverse-primary">{{ $archive->count() }}</p>
            <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">akcí v archivu</p>
        </div>
    </div>
</section>

{{-- ── Upcoming races bento grid ────────────────────────────────── --}}
@if($upcoming->count() > 0)
<section class="py-20">
    <div class="mx-auto max-w-screen-xl px-6 lg:px-8">
        <div class="mb-10 flex items-end justify-between">
            <div>
                <p class="section-eyebrow">Sezóna</p>
                <h2 class="mt-2 font-headline text-4xl text-on-surface dark:text-[#e5e2dd]">Nadcházející akce</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            @foreach($upcoming as $i => $udalost)
                @php
                    $closed = ($udalost->uzavierka_prihlasek && $udalost->uzavierka_prihlasek->lt(now()->startOfDay()))
                           || ($udalost->kapacita !== null && $udalost->pocet_prihlasek >= $udalost->kapacita);
                    $isFirst = $i === 0;
                @endphp

                @if($isFirst)
                {{-- Featured large card --}}
                <div class="panel reveal-up overflow-hidden md:col-span-2 md:row-span-2 hover:shadow-xl transition-shadow">
                    <div class="relative h-64"
                         style="background: linear-gradient(135deg, #173809 0%, #2d4f1e 50%, #446733 100%);">
                        <div class="absolute left-4 top-4">
                            <span class="brand-pill">{{ $closed ? 'Uzavřeno' : 'Registrace otevřena' }}</span>
                        </div>
                    </div>
                    <div class="p-8">
                        <h3 class="font-headline text-3xl text-on-surface dark:text-[#e5e2dd]">{{ $udalost->nazev }}</h3>
                        <p class="mt-2 text-on-surface-variant dark:text-[#c3c8bb]">{{ $udalost->misto }} • {{ $udalost->datum_zacatek?->format('d.m.Y') }}</p>
                        @if($udalost->uzavierka_prihlasek)
                            <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">Uzávěrka: {{ $udalost->uzavierka_prihlasek->format('d.m.Y') }}</p>
                        @endif
                        @if($udalost->kapacita)
                            <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">{{ $udalost->pocet_prihlasek }} / {{ $udalost->kapacita }} přihlášek</p>
                        @endif
                        <a href="{{ route('udalosti.show', $udalost) }}" class="button-primary mt-6 inline-flex">Detail akce</a>
                    </div>
                </div>
                @else
                {{-- Smaller cards --}}
                <div class="panel reveal-up flex items-center gap-5 p-6 transition-colors hover:bg-surface-container-low dark:hover:bg-[#2a2a27]">
                    <div class="h-16 w-16 flex-shrink-0 rounded-xl"
                         style="background: linear-gradient(135deg, #173809 0%, #446733 100%);"></div>
                    <div class="min-w-0">
                        <h3 class="font-headline text-lg truncate text-on-surface dark:text-[#e5e2dd]">{{ $udalost->nazev }}</h3>
                        <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">{{ $udalost->datum_zacatek?->format('d.m.Y') }}</p>
                        <a href="{{ route('udalosti.show', $udalost) }}"
                           class="mt-2 inline-block text-xs font-bold uppercase tracking-widest text-primary underline underline-offset-4 dark:text-inverse-primary">
                            Rezervovat místo
                        </a>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── Archive ───────────────────────────────────────────────────── --}}
@if($archive->isNotEmpty())
<section class="bg-surface-container-low py-20 dark:bg-[#252522]">
    <div class="mx-auto max-w-screen-xl px-6 lg:px-8">
        <p class="section-eyebrow mb-6">Archiv</p>
        <div class="space-y-0">
            @foreach($archive as $udalost)
                <div class="flex items-center justify-between py-5
                            {{ !$loop->last ? 'border-b border-outline-variant/20 dark:border-[#43493e]/30' : '' }}">
                    <div>
                        <p class="font-semibold text-on-surface dark:text-[#e5e2dd]">{{ $udalost->nazev }}</p>
                        <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">{{ $udalost->misto }} • {{ $udalost->datum_zacatek?->format('d.m.Y') }}</p>
                    </div>
                    <a href="{{ route('udalosti.show', $udalost) }}"
                       class="ml-4 flex-shrink-0 text-sm font-bold uppercase tracking-widest text-primary transition hover:opacity-70 dark:text-inverse-primary">
                        Zobrazit
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── CTA Banner ───────────────────────────────────────────────── --}}
<section class="relative overflow-hidden py-32">
    <div class="absolute inset-0"
         style="background: linear-gradient(135deg, #173809 0%, #2d4f1e 50%, #446733 100%);"></div>
    <div class="absolute inset-0 backdrop-blur-sm" style="background: rgba(23,56,9,0.6);"></div>
    <div class="relative z-10 mx-auto max-w-3xl px-8 text-center">
        <h2 class="font-headline text-5xl text-white sm:text-6xl">Připraveni na start?</h2>
        <p class="mt-6 text-xl text-white/80">
            Vytvořte účet a přihlaste se na nadcházející závody během pár minut.
        </p>
        <div class="mt-10 flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
            @auth
                <a href="{{ route('dashboard') }}" class="bg-white px-10 py-4 rounded-lg text-sm font-bold uppercase tracking-widest text-primary hover:bg-stone-100 transition shadow-2xl">
                    Otevřít aplikaci
                </a>
            @else
                <a href="{{ route('register') }}" class="bg-white px-10 py-4 rounded-lg text-sm font-bold uppercase tracking-widest text-primary hover:bg-stone-100 transition shadow-2xl">
                    Vytvořit profil jezdce
                </a>
                <a href="{{ route('udalosti.index') }}" class="border-2 border-white/30 px-10 py-4 rounded-lg text-sm font-bold uppercase tracking-widest text-white hover:bg-white/10 transition">
                    Prohlédnout akce
                </a>
            @endauth
        </div>
    </div>
</section>

</x-site-layout>
