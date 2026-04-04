<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin • Startky • {{ $udalost->nazev }}</h2>
            <div class="text-sm flex items-center gap-2">
                <a href="{{ route('admin.reports.prihlasky', $udalost) }}" class="text-indigo-600 underline">Přihlášky</a>
                <a href="{{ route('admin.reports.export.startky', $udalost) }}" class="text-indigo-600 underline">Export startky</a>
                <a href="{{ route('admin.reports.export.discipliny-pocty', $udalost) }}" class="text-indigo-600 underline">Export počty</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
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
                <div class="bg-white shadow sm:rounded-lg p-4 text-sm text-gray-600">Událost nemá disciplíny.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
