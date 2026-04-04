<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin • Ustájení/Ubytování • {{ $udalost->nazev }}</h2>
            <div class="text-sm flex items-center gap-2">
                <a href="{{ route('admin.reports.prihlasky', $udalost) }}" class="text-indigo-600 underline">Přihlášky</a>
                <a href="{{ route('admin.reports.export.ubytovani', $udalost) }}" class="text-indigo-600 underline">Export</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @forelse(['ustajeni' => 'Ustájení', 'ubytovani' => 'Ubytování', 'strava' => 'Strava', 'ostatni' => 'Ostatní'] as $type => $label)
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
            @empty
            @endforelse
        </div>
    </div>
</x-app-layout>
