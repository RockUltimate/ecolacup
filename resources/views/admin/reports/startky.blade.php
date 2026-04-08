<x-app-layout>
    @php
        $startkyFilters = $startkyFilters ?? ['moznost_id' => 0, 'q' => ''];
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="section-eyebrow">Startky</p>
                <h1 class="text-3xl text-[#20392c]">Startovní listiny podle disciplín</h1>
                <p class="max-w-3xl text-sm leading-6 text-gray-600">{{ $udalost->nazev }} • filtrování podle disciplíny a jména jezdce nebo koně.</p>
            </div>
            <a href="{{ route('admin.udalosti.edit', $udalost) }}" class="button-secondary">Nastavení události</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6">
            @include('admin.udalosti._tabs', ['udalost' => $udalost, 'active' => 'startky'])

            <x-admin-report-filter-form
                :action="route('admin.reports.startky', $udalost)"
                :reset-href="route('admin.reports.startky', $udalost)"
                :form-class="'grid grid-cols-1 md:grid-cols-[220px_minmax(0,1fr)_auto] gap-3 items-end'"
            >
                <div>
                    <x-input-label for="moznost_id" :value="'Disciplína'" />
                    <select id="moznost_id" name="moznost_id" class="field-shell">
                        <option value="0">Všechny disciplíny</option>
                        @foreach($moznostiOptions as $moznost)
                            <option value="{{ $moznost->id }}" @selected((int) $startkyFilters['moznost_id'] === (int) $moznost->id)>{{ $moznost->nazev }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="q" :value="'Hledat (jezdec nebo kůň)'" />
                    <x-text-input id="q" name="q" type="text" :value="$startkyFilters['q']" />
                </div>
            </x-admin-report-filter-form>

            <section class="panel p-5">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.reports.export.startky', $udalost) }}" class="button-secondary">Export startky</a>
                    <a href="{{ route('admin.reports.export.discipliny-pocty', $udalost) }}" class="button-secondary">Export počty</a>
                </div>
            </section>

            <section class="panel p-5 text-sm text-gray-700">
                @if($moznostiSeStartkami->total() > 0)
                    Zobrazeno {{ $moznostiSeStartkami->firstItem() }}–{{ $moznostiSeStartkami->lastItem() }} z {{ $moznostiSeStartkami->total() }} disciplín.
                @else
                    Zobrazeno 0 z 0 disciplín.
                @endif
            </section>

            @forelse($moznostiSeStartkami as $block)
                <details class="panel overflow-hidden" @if($loop->first) open @endif>
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-5">
                        <span class="text-xl font-semibold text-[#20392c]">{{ $block['moznost']->nazev }}</span>
                        <span class="brand-pill">{{ $block['registrations']->count() }} startů</span>
                    </summary>
                    <div class="space-y-3 border-t border-[#eadfcc] px-6 py-5">
                        @forelse($block['registrations'] as $p)
                            <div class="surface-muted">
                                <p class="font-semibold text-[#20392c]">#{{ $p->start_cislo ?? '—' }} • {{ $p->osoba?->prijmeni }} {{ $p->osoba?->jmeno }}{{ $p->vekKategorie() }}</p>
                                <p class="mt-1 text-sm text-gray-600">{{ $p->kun?->jmeno }} @if($p->kunTandem) + {{ $p->kunTandem->jmeno }} @endif • {{ $p->osoba?->staj }}</p>
                            </div>
                        @empty
                            <div class="text-sm text-gray-600">Bez startů.</div>
                        @endforelse
                    </div>
                </details>
            @empty
                <div class="panel p-8 text-sm text-gray-600">Pro zvolený filtr nebyly nalezeny žádné startky.</div>
            @endforelse

            <div>
                {{ $moznostiSeStartkami->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
