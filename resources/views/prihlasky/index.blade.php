<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Přihlášky
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-flash-message />
            @php
                $grouped = $prihlasky->groupBy(fn($item) => $item->udalost?->datum_zacatek?->format('Y') ?? $item->created_at?->format('Y'));
            @endphp
            <div class="space-y-6">
                @forelse($grouped as $year => $items)
                    <section class="panel overflow-hidden">
                        <div class="px-5 py-3 border-b border-[#e9decd] bg-[#faf6ef]">
                            <h3 class="text-lg font-semibold text-[#7b5230]">{{ $year }}</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @foreach($items as $prihlaska)
                                @php($deleted = $prihlaska->smazana || $prihlaska->trashed())
                                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="font-medium text-gray-900">{{ $prihlaska->udalost?->nazev }}</p>
                                            <span @class([
                                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-emerald-100 text-emerald-700' => ! $deleted,
                                                'bg-red-100 text-red-700' => $deleted,
                                            ])>
                                                {{ $deleted ? 'Smazaná' : 'Aktivní' }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $prihlaska->osoba?->prijmeni }} {{ $prihlaska->osoba?->jmeno }} • {{ $prihlaska->kun?->jmeno }}
                                        </p>
                                        <p class="text-sm text-gray-600">Termín akce: {{ $prihlaska->udalost?->datum_zacatek?->format('d.m.Y') ?? '—' }}</p>
                                        <p class="text-sm text-gray-600">Cena: {{ number_format((float)$prihlaska->cena_celkem, 2, ',', ' ') }} Kč</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('prihlasky.show', $prihlaska) }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Detail</a>
                                        <a href="{{ route('prihlasky.pdf', $prihlaska) }}" class="text-sm text-gray-700 hover:text-gray-900 underline">PDF</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <div class="panel p-5 text-sm text-gray-600">Zatím nemáte žádné přihlášky.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
