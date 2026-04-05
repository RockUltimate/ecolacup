<x-app-layout>
    @php
        $filters = $filters ?? ['q' => '', 'rok' => '', 'stav' => 'all'];
    @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin • Členství CMT</h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Dashboard</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-flash-message />
            <x-admin-report-filter-form
                :action="route('admin.clenstvi.index')"
                :reset-href="route('admin.clenstvi.index')"
                :form-class="'grid grid-cols-1 md:grid-cols-[220px_220px_minmax(0,1fr)_auto] gap-3 items-end'"
            >
                <div>
                    <x-input-label for="stav" :value="'Stav'" />
                    <select id="stav" name="stav" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="all" @selected($filters['stav'] === 'all')>Vše</option>
                        <option value="active" @selected($filters['stav'] === 'active')>Aktivní</option>
                        <option value="inactive" @selected($filters['stav'] === 'inactive')>Neaktivní</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="rok" :value="'Rok'" />
                    <x-text-input id="rok" name="rok" type="number" class="mt-1 block w-full" :value="$filters['rok']" />
                </div>
                <div>
                    <x-input-label for="q" :value="'Hledat (osoba, e-mail, evid. číslo)'" />
                    <x-text-input id="q" name="q" type="text" class="mt-1 block w-full" :value="$filters['q']" />
                </div>
            </x-admin-report-filter-form>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @forelse($memberships as $membership)
                        <div class="p-4 sm:p-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ $membership->osoba?->prijmeni }} {{ $membership->osoba?->jmeno }}
                                    <span @class([
                                        'ms-2 inline-flex rounded-full px-2 py-0.5 text-xs font-semibold',
                                        'bg-emerald-100 text-emerald-700' => $membership->aktivni,
                                        'bg-amber-100 text-amber-700' => ! $membership->aktivni,
                                    ])>
                                        {{ $membership->aktivni ? 'AKTIVNÍ' : 'NEAKTIVNÍ' }}
                                    </span>
                                </p>
                                <p class="text-sm text-gray-600">
                                    Rok: {{ $membership->rok }} • Cena: {{ number_format((float) $membership->cena, 2, ',', ' ') }} Kč
                                    @if($membership->evidencni_cislo) • Evid. číslo: {{ $membership->evidencni_cislo }} @endif
                                </p>
                            </div>
                            <a href="{{ route('admin.clenstvi.edit', $membership) }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Upravit</a>
                        </div>
                    @empty
                        <div class="p-5 text-sm text-gray-600">Žádná členství nenalezena.</div>
                    @endforelse
                </div>
            </div>

            <div>
                {{ $memberships->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
