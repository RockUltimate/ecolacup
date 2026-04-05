<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Detail přihlášky</p>
            <h1 class="text-3xl text-on-surface dark:text-[#e5e2dd]">Přihláška #{{ $prihlaska->id }}</h1>
            <p class="max-w-3xl text-sm leading-6 text-on-surface-variant dark:text-[#c3c8bb]">Souhrn účastníka, koně, vybraných položek i aktuálního stavu vůči uzávěrce.</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6">
            @php
                $deadlinePassed = $prihlaska->udalost?->uzavierka_prihlasek?->lt(now()->startOfDay()) ?? true;
                $daysLeft = $prihlaska->udalost?->uzavierka_prihlasek?->diffInDays(now()->startOfDay(), false);
                $deleted = $prihlaska->smazana || $prihlaska->trashed();
                $canEdit = ! $deadlinePassed && ! $deleted;
            @endphp

            <section class="editorial-grid items-start">
                <div class="panel p-6 sm:p-8">
                    <div class="flex flex-wrap items-center gap-3">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-primary dark:text-inverse-primary">{{ $prihlaska->udalost?->misto }}</p>
                        <span @class([
                            'brand-pill',
                            'bg-primary-fixed text-on-primary-fixed' => ! $deleted,
                            'bg-error-container text-on-error-container' => $deleted,
                        ])>
                            {{ $deleted ? 'Smazaná přihláška' : 'Aktivní přihláška' }}
                        </span>
                    </div>

                    <h2 class="mt-4 text-3xl text-on-surface dark:text-[#e5e2dd]">{{ $prihlaska->udalost?->nazev }}</h2>
                    <p class="mt-4 text-sm leading-6 text-on-surface-variant dark:text-[#c3c8bb]">
                        Termín {{ $prihlaska->udalost?->datum_zacatek?->format('d.m.Y') }}
                        @if($prihlaska->udalost?->datum_konec && $prihlaska->udalost->datum_konec->ne($prihlaska->udalost->datum_zacatek))
                            – {{ $prihlaska->udalost->datum_konec->format('d.m.Y') }}
                        @endif
                    </p>

                    <div class="mt-8 grid gap-4 border-t border-outline-variant/30 dark:border-[#43493e]/30 pt-6 sm:grid-cols-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-secondary dark:text-secondary-fixed-dim">Startovní číslo</p>
                            <p class="mt-2 text-2xl font-semibold text-on-surface dark:text-[#e5e2dd]">{{ $prihlaska->start_cislo ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-secondary dark:text-secondary-fixed-dim">Celkem</p>
                            <p class="mt-2 text-2xl font-semibold text-on-surface dark:text-[#e5e2dd]">{{ number_format((float) $prihlaska->cena_celkem, 2, ',', ' ') }} Kč</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-secondary dark:text-secondary-fixed-dim">Uzávěrka</p>
                            <p class="mt-2 text-sm font-semibold text-on-surface dark:text-[#e5e2dd]">
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
                    <div class="mt-4 flex flex-wrap gap-3">
                        @if($canEdit)
                            <a href="{{ route('prihlasky.edit', $prihlaska) }}" class="button-primary">Upravit</a>
                        @endif
                        <a href="{{ route('prihlasky.pdf', $prihlaska) }}" class="button-secondary">Stáhnout PDF</a>
                    </div>

                    @if(! $deleted)
                        <form method="POST" action="{{ route('prihlasky.destroy', $prihlaska) }}" class="mt-4" onsubmit="return confirm('Opravdu smazat přihlášku?');">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm text-error underline underline-offset-4">Smazat přihlášku</button>
                        </form>
                    @endif
                </aside>
            </section>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="panel p-6 sm:p-8">
                    <p class="section-eyebrow">Účastník</p>
                    <h3 class="mt-3 text-2xl text-on-surface dark:text-[#e5e2dd]">{{ $prihlaska->osoba?->prijmeni }} {{ $prihlaska->osoba?->jmeno }}{{ $prihlaska->vekKategorie() }}</h3>
                    <div class="mt-5 grid gap-3 text-sm text-on-surface-variant dark:text-[#c3c8bb] sm:grid-cols-2">
                        <p>Stáj: {{ $prihlaska->osoba?->staj }}</p>
                        <p>Narození: {{ $prihlaska->osoba?->datum_narozeni?->format('d.m.Y') ?? '—' }}</p>
                    </div>
                </section>

                <section class="panel p-6 sm:p-8">
                    <p class="section-eyebrow">Kůň</p>
                    <h3 class="mt-3 text-2xl text-on-surface dark:text-[#e5e2dd]">{{ $prihlaska->kun?->jmeno }}</h3>
                    <div class="mt-5 grid gap-3 text-sm text-on-surface-variant dark:text-[#c3c8bb] sm:grid-cols-2">
                        <p>Plemeno: {{ $prihlaska->kun?->plemeno_kod ?: 'Neuvedeno' }}</p>
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
                    <div class="mt-5 space-y-0">
                        @foreach($prihlaska->polozky as $item)
                            <div class="flex justify-between py-4 {{ !$loop->last ? 'border-b border-outline-variant/20 dark:border-[#43493e]/30' : '' }}">
                                <span class="font-semibold text-on-surface dark:text-[#e5e2dd]">{{ $item->nazev }}</span>
                                <span class="text-sm font-semibold text-on-surface dark:text-[#e5e2dd]">{{ number_format((float) $item->cena, 2, ',', ' ') }} Kč</span>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="panel p-6 sm:p-8">
                    <p class="section-eyebrow">Ustájení a doplňky</p>
                    <div class="mt-5 space-y-0">
                        @forelse($prihlaska->ustajeniChoices as $item)
                            <div class="flex justify-between py-4 {{ !$loop->last ? 'border-b border-outline-variant/20 dark:border-[#43493e]/30' : '' }}">
                                <span class="font-semibold text-on-surface dark:text-[#e5e2dd]">{{ $item->ustajeni?->nazev }}</span>
                                <span class="text-sm font-semibold text-on-surface dark:text-[#e5e2dd]">{{ number_format((float) $item->cena, 2, ',', ' ') }} Kč</span>
                            </div>
                        @empty
                            <div class="py-4 text-sm text-on-surface-variant dark:text-[#c3c8bb]">Bez doplňkových položek.</div>
                        @endforelse
                    </div>
                </section>
            </div>

            @if($prihlaska->poznamka)
                <section class="panel p-6 sm:p-8">
                    <p class="section-eyebrow">Poznámka</p>
                    <p class="mt-4 whitespace-pre-line text-sm leading-7 text-on-surface dark:text-[#e5e2dd]">{{ $prihlaska->poznamka }}</p>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
