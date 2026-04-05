<x-app-layout>
    @php
        $filters = $filters ?? ['q' => '', 'rok' => '', 'stav' => 'all'];
    @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-on-surface dark:text-[#e5e2dd] leading-tight">Admin • Členství CMT</h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-primary dark:text-inverse-primary hover:underline underline">Dashboard</a>
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
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Stav</label>
                    <select id="stav" name="stav" class="field-shell mt-1 block w-full border-outline-variant dark:border-[#43493e] rounded-md shadow-sm">
                        <option value="all" @selected($filters['stav'] === 'all')>Vše</option>
                        <option value="active" @selected($filters['stav'] === 'active')>Aktivní</option>
                        <option value="inactive" @selected($filters['stav'] === 'inactive')>Neaktivní</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Rok</label>
                    <input id="rok" name="rok" type="number" class="field-shell mt-1 block w-full" value="{{ $filters['rok'] }}" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Hledat (osoba, e-mail, evid. číslo)</label>
                    <input id="q" name="q" type="text" class="field-shell mt-1 block w-full" value="{{ $filters['q'] }}" />
                </div>
            </x-admin-report-filter-form>

            <div class="bg-surface-container-lowest dark:bg-[#252522] shadow sm:rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @forelse($memberships as $membership)
                        <div class="p-4 sm:p-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-medium text-on-surface dark:text-[#e5e2dd]">
                                    {{ $membership->osoba?->prijmeni }} {{ $membership->osoba?->jmeno }}
                                    <span @class([
                                        'ms-2 inline-flex rounded-full px-2 py-0.5 text-xs font-semibold',
                                        'bg-primary-fixed text-on-primary-fixed' => $membership->aktivni,
                                        'bg-tertiary-fixed text-on-tertiary-fixed' => ! $membership->aktivni,
                                    ])>
                                        {{ $membership->aktivni ? 'AKTIVNÍ' : 'NEAKTIVNÍ' }}
                                    </span>
                                </p>
                                <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">
                                    Rok: {{ $membership->rok }} • Cena: {{ number_format((float) $membership->cena, 2, ',', ' ') }} Kč
                                    @if($membership->evidencni_cislo) • Evid. číslo: {{ $membership->evidencni_cislo }} @endif
                                </p>
                            </div>
                            <a href="{{ route('admin.clenstvi.edit', $membership) }}" class="text-sm text-primary dark:text-inverse-primary hover:underline underline">Upravit</a>
                        </div>
                    @empty
                        <div class="p-5 text-sm text-on-surface-variant dark:text-[#c3c8bb]">Žádná členství nenalezena.</div>
                    @endforelse
                </div>
            </div>

            <div>
                {{ $memberships->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
