<x-app-layout>
    @php
        $ubytovaniFilters = $ubytovaniFilters ?? ['typ' => 'all', 'q' => ''];
        $typeLabels = ['ustajeni' => 'Ustájení', 'ubytovani' => 'Ubytování', 'strava' => 'Strava', 'ostatni' => 'Ostatní'];
        $hasItems = collect($ustajeniByTyp)->flatten(1)->isNotEmpty();
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="section-eyebrow">Ustájení a služby</p>
                <h1 class="text-3xl text-[#20392c]">Přehled obsazenosti podle typu</h1>
                <p class="max-w-3xl text-sm leading-6 text-gray-600">{{ $udalost->nazev }} • rozpad na ustájení, ubytování, stravu a ostatní služby.</p>
            </div>
            <a href="{{ route('admin.udalosti.edit', $udalost) }}" class="button-secondary">Nastavení události</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6">
            @include('admin.udalosti._tabs', ['udalost' => $udalost, 'active' => 'sluzby'])

            <x-admin-report-filter-form
                :action="route('admin.reports.ubytovani', $udalost)"
                :reset-href="route('admin.reports.ubytovani', $udalost)"
                :form-class="'grid grid-cols-1 md:grid-cols-[220px_minmax(0,1fr)_auto] gap-3 items-end'"
            >
                <div>
                    <x-input-label for="typ" :value="'Typ'" />
                    <select id="typ" name="typ" class="field-shell">
                        <option value="all" @selected($ubytovaniFilters['typ'] === 'all')>Všechny typy</option>
                        @foreach($typeLabels as $key => $label)
                            <option value="{{ $key }}" @selected($ubytovaniFilters['typ'] === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="q" :value="'Hledat (jezdec nebo kůň)'" />
                    <x-text-input id="q" name="q" type="text" :value="$ubytovaniFilters['q']" />
                </div>
            </x-admin-report-filter-form>

            <section class="panel p-5">
                <a href="{{ route('admin.reports.export.ubytovani', $udalost) }}" class="button-secondary">Export ustájení a ubytování</a>
            </section>

            <section class="panel p-5 text-sm text-gray-700">
                @if($optionsPagination->total() > 0)
                    Zobrazeno {{ $optionsPagination->firstItem() }}–{{ $optionsPagination->lastItem() }} z {{ $optionsPagination->total() }} položek.
                @else
                    Zobrazeno 0 z 0 položek.
                @endif
            </section>

            @foreach($typeLabels as $type => $label)
                @if($ubytovaniFilters['typ'] !== 'all' && $ubytovaniFilters['typ'] !== $type)
                    @continue
                @endif

                <section class="panel p-6 sm:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="section-eyebrow">{{ $label }}</p>
                            <h2 class="mt-2 text-2xl text-[#20392c]">{{ $label }}</h2>
                        </div>
                    </div>

                    <div class="mt-6 space-y-5">
                        @forelse(($ustajeniByTyp[$type] ?? collect()) as $item)
                            <div class="space-y-3">
                                <div class="surface-muted">
                                    <p class="font-semibold text-[#20392c]">{{ $item['option']->nazev }}</p>
                                    <p class="mt-1 text-sm text-gray-600">{{ number_format((float) $item['option']->cena, 2, ',', ' ') }} Kč</p>
                                </div>

                                <div class="space-y-3">
                                    @forelse($item['registrations'] as $p)
                                        <div class="rounded-[1rem] border border-[#eadfcc] bg-white/70 px-5 py-4 text-sm text-gray-700">
                                            <p class="font-semibold text-[#20392c]">#{{ $p->start_cislo ?? '—' }} • {{ $p->osoba?->prijmeni }} {{ $p->osoba?->jmeno }}</p>
                                            <p class="mt-1">{{ $p->kun?->jmeno }} ({{ $p->kun?->pohlavi }})</p>
                                        </div>
                                    @empty
                                        <div class="text-sm text-gray-600">Bez přihlášených.</div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-600">Bez položek.</p>
                        @endforelse
                    </div>
                </section>
            @endforeach

            @if(! $hasItems)
                <div class="panel p-8 text-sm text-gray-600">Pro zvolený filtr nebyly nalezeny žádné položky.</div>
            @endif

            <div>
                {{ $optionsPagination->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
