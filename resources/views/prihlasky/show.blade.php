<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Detail přihlášky</p>
            <h1 class="text-3xl text-[#20392c]">Přihláška #{{ $prihlaska->id }}</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">Souhrn účastníka, koně, vybraných položek i aktuálního stavu vůči uzávěrce.</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6">
            @php
                $deadlinePassed = $prihlaska->udalost?->uzavierka_prihlasek?->lt(now()->startOfDay()) ?? true;
                $daysLeft = $prihlaska->udalost?->uzavierka_prihlasek?->diffInDays(now()->startOfDay(), false);
                $deleted = $prihlaska->smazana || $prihlaska->trashed();
                $capacityReached = $prihlaska->udalost?->kapacita !== null && $prihlaska->udalost?->pocet_prihlasek >= $prihlaska->udalost?->kapacita;
                $canEdit = ! $deadlinePassed && ! $deleted;
                $canCreateAnother = $prihlaska->udalost && ! $deadlinePassed && ! $capacityReached;
            @endphp

            <section class="editorial-grid items-start">
                <div class="panel p-6 sm:p-8">
                    <div class="flex flex-wrap items-center gap-3">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#3d6b4f]">{{ $prihlaska->udalost?->misto }}</p>
                        <span @class([
                            'brand-pill',
                            'bg-emerald-100 text-emerald-700' => ! $deleted,
                            'bg-red-100 text-red-700' => $deleted,
                        ])>
                            {{ $deleted ? 'Smazaná přihláška' : 'Aktivní přihláška' }}
                        </span>
                    </div>

                    <h2 class="mt-4 text-3xl text-[#20392c]">{{ $prihlaska->udalost?->nazev }}</h2>
                    <p class="mt-4 text-sm leading-6 text-gray-600">
                        Termín {{ $prihlaska->udalost?->datum_zacatek?->format('d.m.Y') }}
                        @if($prihlaska->udalost?->datum_konec && $prihlaska->udalost->datum_konec->ne($prihlaska->udalost->datum_zacatek))
                            – {{ $prihlaska->udalost->datum_konec->format('d.m.Y') }}
                        @endif
                    </p>

                    <div class="mt-8 grid gap-4 border-t border-[#eadfcc] pt-6 sm:grid-cols-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#7b5230]">Startovní číslo</p>
                            <p class="mt-2 text-2xl font-semibold text-[#20392c]">{{ $prihlaska->start_cislo ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#7b5230]">Celkem</p>
                            <p class="mt-2 text-2xl font-semibold text-[#20392c]">{{ number_format((float) $prihlaska->cena_celkem, 2, ',', ' ') }} Kč</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#7b5230]">Uzávěrka</p>
                            <p class="mt-2 text-sm font-semibold text-[#20392c]">
                                @if($deadlinePassed)
                                    Po uzávěrce
                                @else
                                    Zbývá {{ max(0, (int) $daysLeft) }} dní
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <aside class="panel p-6 sm:p-8">
                    <p class="section-eyebrow">Akce</p>
                    <div class="mt-4 flex w-[170px] max-w-full flex-col gap-3">
                        @if($canCreateAnother)
                            <a href="{{ route('prihlasky.create', $prihlaska->udalost) }}" class="button-secondary w-full">Nová přihláška</a>
                        @endif
                        @if($canEdit)
                            <a href="{{ route('prihlasky.edit', $prihlaska) }}" class="button-primary w-full">Upravit</a>
                        @endif
                        <a href="{{ route('prihlasky.pdf', $prihlaska) }}" class="button-secondary w-full">Stáhnout PDF</a>
                    </div>

                    @if(! $deleted)
                        <form method="POST" action="{{ route('prihlasky.destroy', $prihlaska) }}" class="mt-4 w-[170px] max-w-full" onsubmit="return confirm('Opravdu smazat přihlášku?');">
                            @csrf
                            @method('DELETE')
                            <button class="button-secondary w-full border-red-200 bg-red-50 text-red-700 hover:bg-red-100">Smazat přihlášku</button>
                        </form>
                    @endif
                </aside>
            </section>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="panel p-6 sm:p-8">
                    <p class="section-eyebrow">Účastník</p>
                    <h3 class="mt-3 text-2xl text-[#20392c]">{{ $prihlaska->osoba?->prijmeni }} {{ $prihlaska->osoba?->jmeno }}{{ $prihlaska->vekKategorie() }}</h3>
                    <div class="mt-5 grid gap-3 text-sm text-gray-600 sm:grid-cols-2">
                        <p>Stáj: {{ $prihlaska->osoba?->staj }}</p>
                        <p>Narození: {{ $prihlaska->osoba?->datum_narozeni?->format('d.m.Y') ?? '—' }}</p>
                    </div>
                </section>

                <section class="panel p-6 sm:p-8">
                    <p class="section-eyebrow">Kůň</p>
                    <h3 class="mt-3 text-2xl text-[#20392c]">{{ $prihlaska->kun?->jmeno }}</h3>
                    <div class="mt-5 grid gap-3 text-sm text-gray-600 sm:grid-cols-2">
                        <p>Plemeno: {{ $prihlaska->kun?->plemeno_nazev ?: $prihlaska->kun?->plemeno_vlastni ?: $prihlaska->kun?->plemeno_kod ?: 'Neuvedeno' }}</p>
                        <p>Rok narození: {{ $prihlaska->kun?->rok_narozeni ?? '—' }}</p>
                        @if($prihlaska->kunTandem)
                            <p class="sm:col-span-2">Tandem kůň: {{ $prihlaska->kunTandem->jmeno }}</p>
                        @endif
                    </div>
                </section>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="panel p-6 sm:p-8">
                    <p class="section-eyebrow">Disciplíny</p>
                    <div class="mt-5 space-y-3">
                        @foreach($prihlaska->polozky as $item)
                            <div class="surface-muted flex items-start justify-between gap-4">
                                <p class="font-semibold text-[#20392c]">{{ $item->nazev }}</p>
                                <p class="text-sm font-semibold text-[#7b5230]">{{ number_format((float) $item->cena, 2, ',', ' ') }} Kč</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="panel p-6 sm:p-8">
                    <p class="section-eyebrow">Ustájení a doplňky</p>
                    <div class="mt-5 space-y-3">
                        @forelse($prihlaska->ustajeniChoices as $item)
                            <div class="surface-muted flex items-start justify-between gap-4">
                                <p class="font-semibold text-[#20392c]">{{ $item->ustajeni?->nazev }}</p>
                                <p class="text-sm font-semibold text-[#7b5230]">{{ number_format((float) $item->cena, 2, ',', ' ') }} Kč</p>
                            </div>
                        @empty
                            <div class="surface-muted text-sm text-gray-600">Bez doplňkových položek.</div>
                        @endforelse
                    </div>
                </section>
            </div>

            @if($prihlaska->poznamka)
                <section class="panel p-6 sm:p-8">
                    <p class="section-eyebrow">Poznámka</p>
                    <p class="mt-4 whitespace-pre-line text-sm leading-7 text-gray-700">{{ $prihlaska->poznamka }}</p>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
