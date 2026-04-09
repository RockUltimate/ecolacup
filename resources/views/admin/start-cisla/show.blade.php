<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Admin • Startovní čísla</p>
            <h1 class="text-3xl text-[#20392c]">{{ $udalost->nazev }}</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">Ruční úpravy a srovnání startovních čísel přihlášek v jedné tabulce.</p>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.udalosti.index') }}" class="button-secondary w-full">Zpět na události</a>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-flash-message />
            <div class="bg-white shadow sm:rounded-lg p-4 text-sm flex flex-wrap gap-3">
                <form method="POST" action="{{ route('admin.reports.start-cisla.normalize', $udalost) }}">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="text-indigo-600 underline">Srovnat startovní čísla</button>
                </form>
                <a class="text-indigo-600 underline" href="{{ route('admin.reports.prihlasky', $udalost) }}">Přejít na přihlášky</a>
            </div>
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @forelse($registrations as $registration)
                        <div class="p-4 sm:p-5 flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <p class="font-medium text-gray-900">
                                    #{{ $registration->start_cislo ?? '—' }} • {{ $registration->osoba?->prijmeni }} {{ $registration->osoba?->jmeno }}{{ $registration->vekKategorie() }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    Kůň: {{ $registration->kun?->jmeno }} @if($registration->kunTandem) + {{ $registration->kunTandem->jmeno }} @endif
                                </p>
                            </div>
                            <form method="POST" action="{{ route('admin.reports.start-cislo.update', [$udalost, $registration]) }}" class="flex items-end gap-2">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label for="start_cislo_{{ $registration->id }}" class="block text-xs text-gray-600">Start. číslo</label>
                                    <input id="start_cislo_{{ $registration->id }}" name="start_cislo" type="number" min="1" class="mt-1 w-24 border-gray-300 rounded-md shadow-sm text-sm" value="{{ $registration->start_cislo }}">
                                </div>
                                <button type="submit" class="inline-flex items-center px-3 py-2 text-xs font-semibold uppercase rounded-md bg-indigo-600 text-white hover:bg-indigo-500">Uložit</button>
                            </form>
                        </div>
                    @empty
                        <div class="p-5 text-sm text-gray-600">Žádné aktivní přihlášky.</div>
                    @endforelse
                </div>
            </div>
            <div>
                {{ $registrations->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
