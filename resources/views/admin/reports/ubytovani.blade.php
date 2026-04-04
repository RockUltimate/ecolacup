<x-app-layout>
    @php
        $ubytovaniFilters = $ubytovaniFilters ?? ['typ' => 'all', 'q' => ''];
        $typeLabels = ['ustajeni' => 'Ustájení', 'ubytovani' => 'Ubytování', 'strava' => 'Strava', 'ostatni' => 'Ostatní'];
        $hasItems = collect($ustajeniByTyp)->flatten(1)->isNotEmpty();
    @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin • Ustájení/Ubytování • {{ $udalost->nazev }}</h2>
            <a href="{{ route('admin.udalosti.show', $udalost) }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Přehled události</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @include('admin.udalosti._tabs', ['udalost' => $udalost, 'active' => 'ubytovani'])
            <x-admin-report-filter-form
                :action="route('admin.reports.ubytovani', $udalost)"
                :reset-href="route('admin.reports.ubytovani', $udalost)"
                :form-class="'grid grid-cols-1 md:grid-cols-[220px_minmax(0,1fr)_auto] gap-3 items-end'"
            >
                    <div>
                        <x-input-label for="typ" :value="'Typ'" />
                        <select id="typ" name="typ" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="all" @selected($ubytovaniFilters['typ'] === 'all')>Všechny typy</option>
                            @foreach($typeLabels as $key => $label)
                                <option value="{{ $key }}" @selected($ubytovaniFilters['typ'] === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="q" :value="'Hledat (jezdec/kůň)'" />
                        <x-text-input id="q" name="q" type="text" class="mt-1 block w-full" :value="$ubytovaniFilters['q']" />
                    </div>
            </x-admin-report-filter-form>
            <div class="panel p-3 text-sm">
                <a href="{{ route('admin.reports.export.ubytovani', $udalost) }}" class="text-indigo-600 underline">Export ustájení/ubytování</a>
            </div>
            <div class="panel p-3 text-sm text-gray-700">
                @if($optionsPagination->total() > 0)
                    Zobrazeno {{ $optionsPagination->firstItem() }}–{{ $optionsPagination->lastItem() }} z {{ $optionsPagination->total() }} položek.
                @else
                    Zobrazeno 0 z 0 položek.
                @endif
            </div>
            @foreach($typeLabels as $type => $label)
                @if($ubytovaniFilters['typ'] !== 'all' && $ubytovaniFilters['typ'] !== $type)
                    @continue
                @endif
                <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                    <div class="p-4 border-b border-gray-200 font-semibold text-gray-900">{{ $label }}</div>
                    <div class="p-4 space-y-4">
                        @forelse(($ustajeniByTyp[$type] ?? collect()) as $item)
                            <div>
                                <p class="font-medium text-gray-900">{{ $item['option']->nazev }} ({{ number_format((float)$item['option']->cena, 2, ',', ' ') }} Kč)</p>
                                <ul class="list-disc ps-5 text-sm text-gray-700 mt-1">
                                    @forelse($item['registrations'] as $p)
                                        <li>#{{ $p->start_cislo ?? '—' }} • {{ $p->osoba?->prijmeni }} {{ $p->osoba?->jmeno }} • {{ $p->kun?->jmeno }} ({{ $p->kun?->pohlavi }})</li>
                                    @empty
                                        <li>Bez přihlášených.</li>
                                    @endforelse
                                </ul>
                            </div>
                        @empty
                            <p class="text-sm text-gray-600">Bez položek.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
            @if(! $hasItems)
                <div class="bg-white shadow sm:rounded-lg p-4 text-sm text-gray-600">Pro zvolený filtr nebyly nalezeny žádné položky.</div>
            @endif
            <div>
                {{ $optionsPagination->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
