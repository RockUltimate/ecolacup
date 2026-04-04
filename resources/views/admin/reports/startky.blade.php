<x-app-layout>
    @php
        $startkyFilters = $startkyFilters ?? ['moznost_id' => 0, 'q' => ''];
    @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin • Startky • {{ $udalost->nazev }}</h2>
            <a href="{{ route('admin.udalosti.show', $udalost) }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Přehled události</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @include('admin.udalosti._tabs', ['udalost' => $udalost, 'active' => 'startky'])
            <div class="panel p-4">
                <form method="GET" action="{{ route('admin.reports.startky', $udalost) }}" class="grid grid-cols-1 md:grid-cols-[220px_minmax(0,1fr)_auto] gap-3 items-end">
                    <div>
                        <x-input-label for="moznost_id" :value="'Disciplína'" />
                        <select id="moznost_id" name="moznost_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="0">Všechny disciplíny</option>
                            @foreach($moznostiOptions as $moznost)
                                <option value="{{ $moznost->id }}" @selected((int) $startkyFilters['moznost_id'] === (int) $moznost->id)>{{ $moznost->nazev }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="q" :value="'Hledat (jezdec/kůň)'" />
                        <x-text-input id="q" name="q" type="text" class="mt-1 block w-full" :value="$startkyFilters['q']" />
                    </div>
                    <div class="flex items-center gap-2">
                        <x-primary-button>Filtrovat</x-primary-button>
                        <a href="{{ route('admin.reports.startky', $udalost) }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Reset</a>
                    </div>
                </form>
            </div>
            <div class="panel p-3 text-sm flex flex-wrap gap-3">
                <a href="{{ route('admin.reports.export.startky', $udalost) }}" class="text-indigo-600 underline">Export startky</a>
                <a href="{{ route('admin.reports.export.discipliny-pocty', $udalost) }}" class="text-indigo-600 underline">Export počty</a>
            </div>
            @forelse($moznostiSeStartkami as $block)
                <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                    <div class="p-4 border-b border-gray-200 font-semibold text-gray-900">{{ $block['moznost']->nazev }}</div>
                    <div class="divide-y divide-gray-200">
                        @forelse($block['registrations'] as $p)
                            <div class="p-4 text-sm text-gray-700">
                                #{{ $p->start_cislo ?? '—' }} • {{ $p->osoba?->prijmeni }} {{ $p->osoba?->jmeno }}{{ $p->vekKategorie() }} •
                                {{ $p->kun?->jmeno }} @if($p->kunTandem) + {{ $p->kunTandem->jmeno }} @endif • {{ $p->osoba?->staj }}
                            </div>
                        @empty
                            <div class="p-4 text-sm text-gray-600">Bez startů.</div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="bg-white shadow sm:rounded-lg p-4 text-sm text-gray-600">Pro zvolený filtr nebyly nalezeny žádné startky.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
