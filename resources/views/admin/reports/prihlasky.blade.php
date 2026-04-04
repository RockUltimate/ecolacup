<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin • {{ $showDeleted ?? false ? 'Smazané přihlášky' : 'Přihlášky' }} • {{ $udalost->nazev }}
            </h2>
            <a href="{{ route('admin.udalosti.show', $udalost) }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Přehled události</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @include('admin.udalosti._tabs', ['udalost' => $udalost, 'active' => 'prihlasky'])
            <div class="panel p-3 text-sm flex flex-wrap gap-3">
                <a href="{{ route('admin.reports.prihlasky', $udalost) }}" @class(['underline', 'font-semibold text-[#3d6b4f]' => !($showDeleted ?? false), 'text-indigo-600' => ($showDeleted ?? false)])>Aktivní</a>
                <a href="{{ route('admin.reports.smazane', $udalost) }}" @class(['underline', 'font-semibold text-[#3d6b4f]' => ($showDeleted ?? false), 'text-indigo-600' => !($showDeleted ?? false)])>Smazané</a>
            </div>
            <div class="bg-white shadow sm:rounded-lg p-4 text-sm flex flex-wrap gap-3">
                <a class="text-indigo-600 underline" href="{{ route('admin.reports.export.seznam', $udalost) }}">Export seznam</a>
                <a class="text-indigo-600 underline" href="{{ route('admin.reports.export.discipliny', $udalost) }}">Export disciplíny</a>
                <a class="text-indigo-600 underline" href="{{ route('admin.reports.export.emaily', $udalost) }}">Export e-maily</a>
                <a class="text-indigo-600 underline" href="{{ route('admin.reports.export.kone', $udalost) }}">Export vet</a>
                <a class="text-indigo-600 underline" href="{{ route('admin.reports.export.bulk-pdf', $udalost) }}">Bulk PDF ZIP</a>
            </div>
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @forelse($prihlasky as $p)
                        <div class="p-4 sm:p-5">
                            <p class="font-medium text-gray-900">
                                #{{ $p->start_cislo ?? '—' }} • {{ $p->osoba?->prijmeni }} {{ $p->osoba?->jmeno }}{{ $p->vekKategorie() }}
                            </p>
                            <p class="text-sm text-gray-600">
                                Kůň: {{ $p->kun?->jmeno }} @if($p->kunTandem) + {{ $p->kunTandem->jmeno }} @endif •
                                Cena: {{ number_format((float)$p->cena_celkem, 2, ',', ' ') }} Kč
                            </p>
                        </div>
                    @empty
                        <div class="p-5 text-sm text-gray-600">Žádné záznamy.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
