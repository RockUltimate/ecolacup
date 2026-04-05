<x-site-layout>
    @php
        $deadlinePassed = $udalost->uzavierka_prihlasek && $udalost->uzavierka_prihlasek->lt(now()->startOfDay());
        $capacityReached = $udalost->kapacita !== null && $udalost->pocet_prihlasek >= $udalost->kapacita;
        $isClosed = $deadlinePassed || $capacityReached;
        $daysLeft = $udalost->uzavierka_prihlasek ? now()->startOfDay()->diffInDays($udalost->uzavierka_prihlasek, false) : null;
        $capacityPercent = $udalost->kapacita ? min(100, (int) round(($udalost->pocet_prihlasek / $udalost->kapacita) * 100)) : null;
        $stablingByType = $udalost->ustajeniMoznosti->groupBy('typ');
    @endphp

    <section class="px-4 pb-10 pt-10 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="editorial-grid items-start">
                <div class="panel reveal-up overflow-hidden px-6 py-8 sm:px-8 sm:py-10">
                    <p class="section-eyebrow">Detail události</p>
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-[#3d6b4f]">{{ $udalost->misto }}</p>
                        @if($isClosed)
                            <span class="brand-pill bg-red-100 text-red-700">Registrace uzavřena</span>
                        @elseif($daysLeft !== null && $daysLeft <= 7)
                            <span class="brand-pill bg-amber-100 text-amber-700">Uzávěrka brzy</span>
                        @else
                            <span class="brand-pill">Registrace otevřena</span>
                        @endif
                    </div>

                    <div class="mt-5 max-w-3xl">
                        <h1 class="text-4xl leading-tight text-[#20392c] sm:text-5xl">{{ $udalost->nazev }}</h1>
                        <p class="mt-5 text-base leading-7 text-gray-600">{{ $udalost->popis ?: 'Přehled termínu, disciplín, ustájení a registrace pro tento závod.' }}</p>
                    </div>

                    <div class="mt-8 grid gap-4 border-t border-[#eadfcc] pt-6 sm:grid-cols-2 xl:grid-cols-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#7b5230]">Termín</p>
                            <p class="mt-2 text-lg font-semibold text-[#20392c]">{{ $udalost->datum_zacatek?->format('d.m.Y') }}</p>
                            @if($udalost->datum_konec && $udalost->datum_konec->ne($udalost->datum_zacatek))
                                <p class="text-sm text-gray-600">až do {{ $udalost->datum_konec->format('d.m.Y') }}</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#7b5230]">Uzávěrka</p>
                            <p class="mt-2 text-lg font-semibold text-[#20392c]">{{ $udalost->uzavierka_prihlasek?->format('d.m.Y') ?? 'Bez uzávěrky' }}</p>
                            <p class="text-sm text-gray-600">
                                @if($daysLeft === null)
                                    Přihlášky bez termínového omezení.
                                @elseif($daysLeft < 0)
                                    Termín už proběhl.
                                @elseif($daysLeft === 0)
                                    Končí dnes.
                                @else
                                    Zbývá {{ $daysLeft }} dní.
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#7b5230]">Registrace</p>
                            <p class="mt-2 text-lg font-semibold text-[#20392c]">{{ $udalost->pocet_prihlasek }}</p>
                            <p class="text-sm text-gray-600">celkem evidovaných přihlášek</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#7b5230]">Starty</p>
                            <p class="mt-2 text-lg font-semibold text-[#20392c]">{{ $udalost->pocet_startu }}</p>
                            <p class="text-sm text-gray-600">obsazených disciplín</p>
                        </div>
                    </div>
                </div>

                <aside class="panel reveal-up-delay space-y-6 px-6 py-8 sm:px-8">
                    <div>
                        <p class="section-eyebrow">Registrace</p>
                        <h2 class="mt-3 text-2xl text-[#20392c]">
                            @if($isClosed)
                                Tato akce už nepřijímá další přihlášky.
                            @else
                                Přihlášení mohou pokračovat rovnou do formuláře.
                            @endif
                        </h2>
                        <p class="mt-3 text-sm leading-6 text-gray-600">Po přihlášení navážete na existující osoby a koně ve svém účtu, takže další registrace jsou výrazně rychlejší.</p>
                    </div>

                    @if($capacityPercent !== null)
                        <div class="rounded-[1.25rem] border border-[#eadfcc] bg-[#f9f4eb] p-5">
                            <div class="flex items-center justify-between gap-4 text-sm text-gray-600">
                                <span>Využití kapacity</span>
                                <span>{{ $udalost->pocet_prihlasek }} / {{ $udalost->kapacita }}</span>
                            </div>
                            <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-[#e6d8c6]">
                                <div class="h-full rounded-full bg-[#3d6b4f]" style="width: {{ $capacityPercent }}%"></div>
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-3">
                        @auth
                            @if($isClosed)
                                <span class="button-secondary cursor-default opacity-70">Přihlášky uzavřeny</span>
                            @else
                                <a href="{{ route('prihlasky.create', $udalost) }}" class="button-primary">Přihlásit se na akci</a>
                            @endif
                            <a href="{{ route('prihlasky.index') }}" class="button-secondary">Moje přihlášky</a>
                        @else
                            <a href="{{ route('login') }}" class="button-primary">Přihlásit se</a>
                            <a href="{{ route('register') }}" class="button-secondary">Vytvořit účet</a>
                        @endauth
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto grid max-w-7xl gap-6 lg:grid-cols-[minmax(0,1.15fr)_minmax(320px,0.85fr)]">
            <div class="panel p-6 sm:p-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="section-eyebrow">Disciplíny</p>
                        <h2 class="mt-2 text-3xl text-[#20392c]">Startovní program</h2>
                    </div>
                    <p class="text-sm text-gray-500">{{ $udalost->moznosti->count() }} položek</p>
                </div>

                <div class="mt-8 space-y-3">
                    @forelse($udalost->moznosti->sortBy('poradi') as $moznost)
                        <div class="grid gap-3 rounded-[1.25rem] border border-[#eadfcc] bg-white/70 px-5 py-4 sm:grid-cols-[minmax(0,1fr)_110px_110px] sm:items-center">
                            <div>
                                <p class="font-semibold text-[#20392c]">{{ $moznost->nazev }}</p>
                                <p class="mt-1 text-sm text-gray-600">
                                    @if($moznost->je_administrativni_poplatek)
                                        Administrativní položka přidávaná podle pravidel členství.
                                    @elseif($moznost->min_vek !== null)
                                        Minimální věk účastníka: {{ $moznost->min_vek }} let.
                                    @else
                                        Bez věkového omezení.
                                    @endif
                                </p>
                            </div>
                            <div class="text-sm text-gray-600">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#7b5230]">Min. věk</p>
                                <p class="mt-1 font-semibold text-[#20392c]">{{ $moznost->min_vek ?? '—' }}</p>
                            </div>
                            <div class="text-sm text-gray-600 sm:text-right">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#7b5230]">Cena</p>
                                <p class="mt-1 font-semibold text-[#20392c]">{{ number_format((float) $moznost->cena, 2, ',', ' ') }} Kč</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[1.25rem] border border-[#eadfcc] bg-white/70 px-5 py-4 text-sm text-gray-600">Zatím nejsou přidané žádné disciplíny.</div>
                    @endforelse
                </div>
            </div>

            <div class="panel p-6 sm:p-8">
                <p class="section-eyebrow">Zázemí</p>
                <h2 class="mt-2 text-3xl text-[#20392c]">Ustájení a doplňkové služby</h2>

                <div class="mt-8 space-y-6">
                    @forelse(['ustajeni' => 'Ustájení', 'ubytovani' => 'Ubytování', 'strava' => 'Strava', 'ostatni' => 'Ostatní'] as $type => $label)
                        @if(($stablingByType[$type] ?? collect())->isNotEmpty())
                            <div>
                                <h3 class="text-lg font-semibold text-[#20392c]">{{ $label }}</h3>
                                <div class="mt-3 space-y-3">
                                    @foreach($stablingByType[$type] as $moznost)
                                        <div class="rounded-[1.25rem] border border-[#eadfcc] bg-white/70 px-5 py-4">
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <p class="font-semibold text-[#20392c]">{{ $moznost->nazev }}</p>
                                                    <p class="mt-1 text-sm text-gray-600">
                                                        @if($moznost->kapacita)
                                                            Kapacita {{ $moznost->kapacita }} míst.
                                                        @else
                                                            Bez pevně stanovené kapacity.
                                                        @endif
                                                    </p>
                                                </div>
                                                <p class="text-sm font-semibold text-[#7b5230]">{{ number_format((float) $moznost->cena, 2, ',', ' ') }} Kč</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @empty
                    @endforelse

                    @if($udalost->ustajeniMoznosti->isEmpty())
                        <div class="rounded-[1.25rem] border border-[#eadfcc] bg-white/70 px-5 py-4 text-sm text-gray-600">Zatím nejsou přidané žádné možnosti ustájení ani doplňkových služeb.</div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-site-layout>
