<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Moje přihlášky</p>
            <h1 class="text-3xl text-[#20392c]">Přehled registrací podle ročníků</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">Detail každé přihlášky obsahuje PDF, cenu, termín akce i stav po uzávěrce.</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl">
            @php
                $grouped = $prihlasky->groupBy(fn ($item) => $item->udalost?->datum_zacatek?->format('Y') ?? $item->created_at?->format('Y'));
            @endphp

            <div class="space-y-8">
                @forelse($grouped as $year => $items)
                    <section class="space-y-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="section-eyebrow">Ročník</p>
                                <h2 class="mt-2 text-2xl text-[#20392c]">{{ $year }}</h2>
                            </div>
                            <p class="text-sm text-gray-500">{{ $items->count() }} přihlášek</p>
                        </div>

                        <div class="space-y-4">
                            @foreach($items as $prihlaska)
                                @php($deleted = $prihlaska->smazana || $prihlaska->trashed())
                                <a href="{{ route('prihlasky.show', $prihlaska) }}" class="panel p-6 flex items-center justify-between gap-6 transition hover:shadow-md">
                                    <div class="min-w-0 space-y-3">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <p class="text-xl font-semibold text-[#20392c]">{{ $prihlaska->udalost?->nazev }}</p>
                                            <span @class([
                                                'brand-pill',
                                                'bg-emerald-100 text-emerald-700' => ! $deleted,
                                                'bg-red-100 text-red-700' => $deleted,
                                            ])>
                                                {{ $deleted ? 'Smazaná' : 'Aktivní' }}
                                            </span>
                                        </div>
                                        <div class="grid gap-2 text-sm text-gray-600 sm:grid-cols-2">
                                            <p>{{ $prihlaska->osoba?->prijmeni }} {{ $prihlaska->osoba?->jmeno }}</p>
                                            <p>Kůň: {{ $prihlaska->kun?->jmeno }}</p>
                                            <p>Termín: {{ $prihlaska->udalost?->datum_zacatek?->format('d.m.Y') ?? '—' }}</p>
                                            <p>Cena: {{ number_format((float) $prihlaska->cena_celkem, 2, ',', ' ') }} Kč</p>
                                        </div>
                                    </div>
                                    <div class="shrink-0" onclick="event.preventDefault(); window.location='{{ route('prihlasky.pdf', $prihlaska) }}'">
                                        <span class="button-secondary">PDF</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <div class="panel p-8 text-sm leading-6 text-gray-600">Zatím nemáte žádné přihlášky. Začněte z detailu vybrané události.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
