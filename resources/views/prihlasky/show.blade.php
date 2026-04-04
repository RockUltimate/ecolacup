<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Přihláška #{{ $prihlaska->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-message />
            @php
                $deadlinePassed = $prihlaska->udalost?->uzavierka_prihlasek?->lt(now()->startOfDay()) ?? true;
                $daysLeft = $prihlaska->udalost?->uzavierka_prihlasek?->diffInDays(now()->startOfDay(), false);
                $deleted = $prihlaska->smazana || $prihlaska->trashed();
                $canEdit = ! $deadlinePassed && ! $deleted;
            @endphp

            <div class="panel p-6">
                <h3 class="font-semibold text-gray-900">{{ $prihlaska->udalost?->nazev }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ $prihlaska->udalost?->misto }}</p>
                <p class="text-sm text-gray-600 mt-1">Termín: {{ $prihlaska->udalost?->datum_zacatek?->format('d.m.Y') }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span @class([
                        'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                        'bg-emerald-100 text-emerald-700' => ! $deleted,
                        'bg-red-100 text-red-700' => $deleted,
                    ])>
                        {{ $deleted ? 'Smazaná přihláška' : 'Aktivní přihláška' }}
                    </span>
                    <span @class([
                        'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                        'bg-amber-100 text-amber-700' => $deadlinePassed,
                        'bg-blue-100 text-blue-700' => ! $deadlinePassed,
                    ])>
                        {{ $deadlinePassed ? 'Po uzávěrce' : 'Do uzávěrky: '.max(0, (int) $daysLeft).' dní' }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="panel p-6">
                    <h4 class="font-semibold text-gray-900 mb-2">Účastník</h4>
                    <p class="text-sm text-gray-700">{{ $prihlaska->osoba?->prijmeni }} {{ $prihlaska->osoba?->jmeno }}{{ $prihlaska->vekKategorie() }}</p>
                    <p class="text-sm text-gray-600">Stáj: {{ $prihlaska->osoba?->staj }}</p>
                </div>
                <div class="panel p-6">
                    <h4 class="font-semibold text-gray-900 mb-2">Kůň</h4>
                    <p class="text-sm text-gray-700">{{ $prihlaska->kun?->jmeno }}</p>
                    @if($prihlaska->kunTandem)
                        <p class="text-sm text-gray-600">Tandem: {{ $prihlaska->kunTandem->jmeno }}</p>
                    @endif
                </div>
            </div>

            <div class="panel p-6">
                <h4 class="font-semibold text-gray-900 mb-3">Vybrané disciplíny</h4>
                <ul class="space-y-1 text-sm text-gray-700">
                    @foreach($prihlaska->polozky as $item)
                        <li>{{ $item->nazev }} — {{ number_format((float)$item->cena, 2, ',', ' ') }} Kč</li>
                    @endforeach
                </ul>
            </div>

            <div class="panel p-6">
                <h4 class="font-semibold text-gray-900 mb-3">Ustájení / ubytování</h4>
                <ul class="space-y-1 text-sm text-gray-700">
                    @forelse($prihlaska->ustajeniChoices as $item)
                        <li>{{ $item->ustajeni?->nazev }} — {{ number_format((float)$item->cena, 2, ',', ' ') }} Kč</li>
                    @empty
                        <li>Bez doplňkových položek.</li>
                    @endforelse
                </ul>
            </div>

            <div class="panel p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-600">Startovní číslo: {{ $prihlaska->start_cislo ?? '—' }}</p>
                    <p class="text-lg font-semibold text-gray-900">Celkem: {{ number_format((float)$prihlaska->cena_celkem, 2, ',', ' ') }} Kč</p>
                </div>
                <div class="flex items-center gap-3">
                    @if($canEdit)
                        <a href="{{ route('prihlasky.edit', $prihlaska) }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Upravit</a>
                    @endif
                    <a href="{{ route('prihlasky.pdf', $prihlaska) }}" class="text-sm text-gray-700 hover:text-gray-900 underline">Stáhnout PDF</a>
                    @if(! $deleted)
                        <form method="POST" action="{{ route('prihlasky.destroy', $prihlaska) }}" onsubmit="return confirm('Opravdu smazat přihlášku?');">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm text-red-600 hover:text-red-800 underline">Smazat</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
