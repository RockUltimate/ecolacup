<x-app-layout>
    @php
        $filters = $filters ?? ['q' => '', 'stav' => (($showDeleted ?? false) ? 'deleted' : 'active')];
        $duplicateStartNumbers = $duplicateStartNumbers ?? [];
        $isDeletedView = ($showDeleted ?? false);
        $listingRoute = $isDeletedView ? route('admin.reports.smazane', $udalost) : route('admin.reports.prihlasky', $udalost);
    @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin • {{ $isDeletedView ? 'Smazané přihlášky' : 'Přihlášky' }} • {{ $udalost->nazev }}
            </h2>
            <a href="{{ route('admin.udalosti.show', $udalost) }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Přehled události</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @include('admin.udalosti._tabs', ['udalost' => $udalost, 'active' => 'prihlasky'])
            <div class="panel p-3 text-sm flex flex-wrap gap-3">
                <a href="{{ route('admin.reports.prihlasky', $udalost) }}" @class(['underline', 'font-semibold text-[#3d6b4f]' => ! $isDeletedView, 'text-indigo-600' => $isDeletedView])>Aktivní</a>
                <a href="{{ route('admin.reports.smazane', $udalost) }}" @class(['underline', 'font-semibold text-[#3d6b4f]' => $isDeletedView, 'text-indigo-600' => ! $isDeletedView])>Smazané</a>
            </div>
            @if(count($duplicateStartNumbers) > 0)
                <div class="panel p-3 text-sm text-amber-800 bg-amber-50 border-amber-200">
                    Duplicitní startovní čísla: {{ implode(', ', $duplicateStartNumbers) }}
                </div>
            @endif
            <div class="panel p-4">
                <form method="GET" action="{{ $listingRoute }}" class="grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_180px_auto] gap-3 items-end">
                    <div>
                        <x-input-label for="q" :value="'Hledat (startovní číslo, osoba, kůň, e-mail)'" />
                        <x-text-input id="q" name="q" type="text" class="mt-1 block w-full" :value="$filters['q']" />
                    </div>
                    <div>
                        <x-input-label for="stav" :value="'Stav'" />
                        <select id="stav" name="stav" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="active" @selected($filters['stav'] === 'active')>Aktivní</option>
                            <option value="deleted" @selected($filters['stav'] === 'deleted')>Smazané</option>
                            <option value="all" @selected($filters['stav'] === 'all')>Všechny</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-primary-button>Filtrovat</x-primary-button>
                        <a href="{{ $listingRoute }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Reset</a>
                    </div>
                </form>
            </div>
            <div class="bg-white shadow sm:rounded-lg p-4 text-sm flex flex-wrap gap-3">
                @if(! $isDeletedView)
                    <form method="POST" action="{{ route('admin.reports.start-cisla.normalize', $udalost) }}">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="text-indigo-600 underline">Srovnat startovní čísla</button>
                    </form>
                @endif
                <a class="text-indigo-600 underline" href="{{ route('admin.reports.export.seznam', $udalost) }}">Export seznam</a>
                <a class="text-indigo-600 underline" href="{{ route('admin.reports.export.discipliny', $udalost) }}">Export disciplíny</a>
                <a class="text-indigo-600 underline" href="{{ route('admin.reports.export.emaily', $udalost) }}">Export e-maily</a>
                <a class="text-indigo-600 underline" href="{{ route('admin.reports.export.kone', $udalost) }}">Export vet</a>
                <a class="text-indigo-600 underline" href="{{ route('admin.reports.export.bulk-pdf', $udalost) }}">Bulk PDF ZIP</a>
            </div>
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @forelse($prihlasky as $p)
                        <div @class([
                            'p-4 sm:p-5',
                            'bg-red-50/70' => $p->smazana,
                            'ring-1 ring-amber-300 bg-amber-50/70' => $p->start_cislo !== null && in_array((int) $p->start_cislo, $duplicateStartNumbers, true),
                        ])>
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">
                                        #{{ $p->start_cislo ?? '—' }} • {{ $p->osoba?->prijmeni }} {{ $p->osoba?->jmeno }}{{ $p->vekKategorie() }}
                                        @if($p->smazana)
                                            <span class="ms-2 inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">SMAZANÁ</span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Kůň: {{ $p->kun?->jmeno }} @if($p->kunTandem) + {{ $p->kunTandem->jmeno }} @endif •
                                        Cena: {{ number_format((float)$p->cena_celkem, 2, ',', ' ') }} Kč •
                                        E-mail: {{ $p->user?->email }}
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('admin.reports.start-cislo.update', [$udalost, $p]) }}" class="flex items-end gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="q" value="{{ $filters['q'] }}">
                                    <input type="hidden" name="stav" value="{{ $filters['stav'] }}">
                                    <div>
                                        <label for="start_cislo_{{ $p->id }}" class="block text-xs text-gray-600">Start. číslo</label>
                                        <input id="start_cislo_{{ $p->id }}" name="start_cislo" type="number" min="1" class="mt-1 w-24 border-gray-300 rounded-md shadow-sm text-sm" value="{{ $p->start_cislo }}">
                                    </div>
                                    <button type="submit" class="inline-flex items-center px-3 py-2 text-xs font-semibold uppercase rounded-md bg-indigo-600 text-white hover:bg-indigo-500">Uložit</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-sm text-gray-600">Žádné záznamy.</div>
                    @endforelse
                </div>
            </div>
            <div>
                {{ $prihlasky->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
