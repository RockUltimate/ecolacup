<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="section-eyebrow">Událost</p>
                <h1 class="text-3xl text-[#20392c]">{{ $udalost->nazev }}</h1>
                <p class="max-w-3xl text-sm leading-6 text-gray-600">Přehled registrací, kapacity, disciplín a rychlý vstup do pořadatelských reportů.</p>
            </div>
            <a href="{{ route('admin.udalosti.index') }}" class="button-secondary">Zpět na události</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6">
            @include('admin.udalosti._tabs', ['udalost' => $udalost, 'active' => 'popis'])

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="panel p-6">
                    <p class="section-eyebrow">Aktivní přihlášky</p>
                    <p class="mt-3 text-4xl font-semibold text-[#20392c]">{{ number_format($udalost->active_prihlasky_count) }}</p>
                </article>
                <article class="panel p-6">
                    <p class="section-eyebrow">Smazané přihlášky</p>
                    <p class="mt-3 text-4xl font-semibold text-[#20392c]">{{ number_format($udalost->deleted_prihlasky_count) }}</p>
                </article>
                <article class="panel p-6">
                    <p class="section-eyebrow">Disciplíny</p>
                    <p class="mt-3 text-4xl font-semibold text-[#20392c]">{{ number_format($udalost->moznosti_count) }}</p>
                </article>
                <article class="panel p-6">
                    <p class="section-eyebrow">Ustájení a služby</p>
                    <p class="mt-3 text-4xl font-semibold text-[#20392c]">{{ number_format($udalost->ustajeni_moznosti_count) }}</p>
                </article>
            </section>

            <section class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)]">
                <article class="panel p-6 sm:p-8">
                    <p class="section-eyebrow">Poslední aktivita</p>
                    <h2 class="mt-2 text-2xl text-[#20392c]">Poslední přihlášky</h2>
                    <div class="mt-6 space-y-4">
                        @forelse($recentRegistrations as $registration)
                            <div class="surface-muted">
                                <p class="font-semibold text-[#20392c]">#{{ $registration->start_cislo ?? '—' }} • {{ $registration->osoba?->prijmeni }} {{ $registration->osoba?->jmeno }}</p>
                                <p class="mt-1 text-sm text-gray-600">{{ $registration->kun?->jmeno }} • {{ number_format((float) $registration->cena_celkem, 2, ',', ' ') }} Kč</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $registration->created_at?->format('d.m.Y H:i') }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-600">Zatím nejsou žádné aktivní přihlášky.</p>
                        @endforelse
                    </div>
                </article>

                <aside class="panel p-6 sm:p-8">
                    <p class="section-eyebrow">Detaily akce</p>
                    <div class="mt-5 space-y-4 text-sm text-gray-700">
                        <div class="surface-muted">
                            <p class="font-semibold text-[#20392c]">{{ $udalost->misto }}</p>
                            <p class="mt-1">Termín: {{ $udalost->datum_zacatek?->format('d.m.Y') }} @if($udalost->datum_konec && $udalost->datum_konec->ne($udalost->datum_zacatek))– {{ $udalost->datum_konec->format('d.m.Y') }}@endif</p>
                            <p class="mt-1">Uzávěrka: {{ $udalost->uzavierka_prihlasek?->format('d.m.Y') }}</p>
                            <p class="mt-1">Kapacita: {{ $udalost->kapacita ? number_format($udalost->kapacita) : 'Neomezená' }}</p>
                        </div>
                        <div class="surface-muted space-y-2">
                            <p class="font-semibold text-[#20392c]">Materiály akce</p>
                            <p>
                                Propozice:
                                @if($udalost->propozice_pdf)
                                    <a href="{{ asset('storage/'.$udalost->propozice_pdf) }}" target="_blank" rel="noopener" class="text-[#7b5230] underline underline-offset-4">otevřít PDF</a>
                                @else
                                    <span class="text-gray-500">zatím bez souboru</span>
                                @endif
                            </p>
                            <p>
                                Výsledky:
                                @if($udalost->vysledky_pdf)
                                    <a href="{{ asset('storage/'.$udalost->vysledky_pdf) }}" target="_blank" rel="noopener" class="text-[#7b5230] underline underline-offset-4">otevřít PDF</a>
                                @else
                                    <span class="text-gray-500">zatím bez souboru</span>
                                @endif
                            </p>
                            <p>
                                Fotoalbum:
                                @if($udalost->fotoalbum_url)
                                    <a href="{{ $udalost->fotoalbum_url }}" target="_blank" rel="noopener" class="text-[#7b5230] underline underline-offset-4">otevřít album</a>
                                @else
                                    <span class="text-gray-500">zatím bez odkazu</span>
                                @endif
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('admin.start-cisla.show', $udalost) }}" class="button-secondary">Správa startovních čísel</a>
                            <a href="{{ route('admin.udalosti.edit', $udalost) }}" class="button-primary">Upravit událost</a>
                        </div>
                    </div>
                </aside>
            </section>
        </div>
    </div>
</x-app-layout>
