<x-site-layout>
    @php
        $featured = $upcoming->first();
        $remainingUpcoming = $upcoming->slice(1);
        $openEvents = $upcoming->filter(function ($udalost) {
            $deadlinePassed = $udalost->uzavierka_prihlasek?->lt(now()->startOfDay());
            $capacityReached = $udalost->kapacita !== null && $udalost->pocet_prihlasek >= $udalost->kapacita;

            return ! $deadlinePassed && ! $capacityReached;
        })->count();
    @endphp

    <section class="px-4 pb-10 pt-10 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="editorial-grid items-stretch">
                <div class="panel reveal-up overflow-hidden px-6 py-8 sm:px-8 sm:py-10">
                    <p class="section-eyebrow">Kalendar akcí</p>
                    <div class="mt-5 max-w-3xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-[#3d6b4f]">ECOLAKONĚ</p>
                        <h1 class="mt-4 text-4xl leading-tight text-[#20392c] sm:text-5xl">Moderní přihlášky na koňské závody</h1>
                        <p class="mt-5 max-w-2xl text-base leading-7 text-gray-600">Veřejný kalendář, přehled uzávěrek, disciplín a kapacit. Přihlášení jezdci navazují rovnou na správu osob, koní a přihlášek bez ruční administrativy navíc.</p>
                    </div>

                    @guest
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="button-primary">Začít registraci</a>
                        <a href="{{ route('login') }}" class="button-secondary">Mám účet</a>
                    </div>
                    @endguest

                    <div class="mt-10 grid gap-4 border-t border-[#eadfcc] pt-6 sm:grid-cols-3">
                        <div>
                            <p class="text-3xl font-semibold text-[#20392c]">{{ $upcoming->count() }}</p>
                            <p class="mt-1 text-sm text-gray-600">nadcházejících událostí</p>
                        </div>
                        <div>
                            <p class="text-3xl font-semibold text-[#20392c]">{{ $openEvents }}</p>
                            <p class="mt-1 text-sm text-gray-600">právě otevřených registrací</p>
                        </div>
                        <div>
                            <p class="text-3xl font-semibold text-[#20392c]">{{ $archive->count() }}</p>
                            <p class="mt-1 text-sm text-gray-600">akcí v archivu</p>
                        </div>
                    </div>
                </div>

                <aside class="panel reveal-up-delay flex flex-col justify-between px-6 py-8 sm:px-8">
                    <div>
                        <p class="section-eyebrow">Rychlý přehled</p>
                        @if($featured)
                            @php
                                $featuredClosed = ($featured->uzavierka_prihlasek && $featured->uzavierka_prihlasek->lt(now()->startOfDay()))
                                    || ($featured->kapacita !== null && $featured->pocet_prihlasek >= $featured->kapacita);
                            @endphp
                            <div class="mt-5 space-y-4">
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-[#3d6b4f]">Nejbližší akce</p>
                                    <h2 class="mt-3 text-3xl text-[#20392c]">{{ $featured->nazev }}</h2>
                                </div>
                                <div class="space-y-2 text-sm leading-6 text-gray-600">
                                    <p>{{ $featured->misto }}</p>
                                    <p>{{ $featured->datum_zacatek?->format('d.m.Y') }} @if($featured->datum_konec && $featured->datum_konec->ne($featured->datum_zacatek))– {{ $featured->datum_konec->format('d.m.Y') }} @endif</p>
                                    <p>Uzávěrka přihlášek: {{ $featured->uzavierka_prihlasek?->format('d.m.Y') }}</p>
                                </div>
                            </div>
                        @else
                            <p class="mt-5 text-sm leading-6 text-gray-600">Po vypsání první akce se tady zobrazí nejbližší termín s kapacitou a uzávěrkou.</p>
                        @endif
                    </div>

                    @if($featured)
                        <div class="mt-8 rounded-[1.25rem] border border-[#eadfcc] bg-[#f9f4eb] p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#7b5230]">{{ $featuredClosed ? 'Registrace uzavřena' : 'Registrace otevřena' }}</p>
                            <p class="mt-3 text-sm leading-6 text-gray-600">Detail akce obsahuje disciplíny, ustájení, kapacity i okamžitý vstup do přihlášky.</p>
                            <a href="{{ route('udalosti.show', $featured) }}" class="button-secondary mt-5">Zobrazit detail</a>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </section>

    <section class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="flex items-end justify-between gap-6">
                <div>
                    <p class="section-eyebrow">Nadcházející akce</p>
                    <h2 class="mt-2 text-3xl text-[#20392c]">Vypsané termíny a jejich stav</h2>
                </div>
            </div>

            <div class="mt-8 space-y-4">
                @forelse($upcoming as $index => $udalost)
                    @php
                        $deadlinePassed = $udalost->uzavierka_prihlasek?->lt(now()->startOfDay());
                        $capacityReached = $udalost->kapacita !== null && $udalost->pocet_prihlasek >= $udalost->kapacita;
                        $isClosed = $deadlinePassed || $capacityReached;
                        $daysToDeadline = $udalost->uzavierka_prihlasek?->diffInDays(now()->startOfDay(), false);
                    @endphp
                    <a href="{{ route('udalosti.show', $udalost) }}" class="panel block p-6 transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_26px_80px_rgba(71,52,34,0.12)]">
                        <div class="grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(280px,0.6fr)] lg:items-center">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#7b5230]">Akce {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</p>
                                    @if($isClosed)
                                        <span class="brand-pill bg-red-100 text-red-700">Uzavřeno</span>
                                    @elseif($daysToDeadline !== null && $daysToDeadline <= 7)
                                        <span class="brand-pill bg-amber-100 text-amber-700">Uzávěrka brzy</span>
                                    @else
                                        <span class="brand-pill">Otevřeno</span>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-2xl text-[#20392c]">{{ $udalost->nazev }}</h3>
                                    @if($udalost->popis)
                                        <div class="mt-2 max-w-2xl space-y-3 text-sm leading-6 text-gray-600 [&_p]:mt-3 [&_p:first-child]:mt-0 [&_ul]:mt-3 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:mt-3 [&_ol]:list-decimal [&_ol]:pl-5 [&_strong]:font-semibold [&_a]:text-[#7b5230] [&_a]:underline">
                                            {!! $udalost->popis !!}
                                        </div>
                                    @else
                                        <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600">Detail akce obsahuje všechny disciplíny, možnosti ustájení i stav registrace.</p>
                                    @endif
                                </div>
                            </div>

                            <div class="grid gap-3 text-sm text-gray-600 sm:grid-cols-2 lg:grid-cols-1">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#3d6b4f]">Místo a termín</p>
                                    <p class="mt-1 text-base font-semibold text-[#20392c]">{{ $udalost->misto }}</p>
                                    <p>{{ $udalost->datum_zacatek?->format('d.m.Y') }} @if($udalost->datum_konec && $udalost->datum_konec->ne($udalost->datum_zacatek))– {{ $udalost->datum_konec->format('d.m.Y') }} @endif</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#3d6b4f]">Přihlášky</p>
                                    <p class="mt-1">Uzávěrka {{ $udalost->uzavierka_prihlasek?->format('d.m.Y') }}</p>
                                    <p>{{ $udalost->pocet_prihlasek }} registrací / {{ $udalost->pocet_startu }} startů</p>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="panel p-8 text-sm leading-6 text-gray-600">Zatím nejsou vypsané žádné nadcházející akce.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="px-4 pb-14 pt-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,0.75fr)_minmax(0,1.25fr)]">
                <div class="space-y-3">
                    <p class="section-eyebrow">Archiv</p>
                    <h2 class="text-3xl text-[#20392c]">Přehled minulých ročníků</h2>
                    <p class="text-sm leading-6 text-gray-600">Historie zůstává snadno dohledatelná pro pořadatele i účastníky, kteří se vracejí k předchozím akcím a výstupům.</p>
                </div>

                <div class="panel overflow-hidden">
                    <div class="divide-y divide-[#eadfcc]">
                        @forelse($archive as $udalost)
                            <a href="{{ route('udalosti.show', $udalost) }}" class="block px-6 py-5 transition hover:bg-white/60">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-lg font-semibold text-[#20392c]">{{ $udalost->nazev }}</p>
                                        <p class="text-sm text-gray-600">{{ $udalost->misto }}</p>
                                    </div>
                                    <p class="text-sm font-medium text-[#7b5230]">{{ $udalost->datum_zacatek?->format('d.m.Y') }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="px-6 py-8 text-sm text-gray-600">Archiv je zatím prázdný.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-site-layout>
