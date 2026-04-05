<x-app-layout>
    @php
        $filters = $filters ?? ['q' => '', 'stav' => (($showDeleted ?? false) ? 'deleted' : 'active')];
        $duplicateStartNumbers = $duplicateStartNumbers ?? [];
        $isDeletedView = ($showDeleted ?? false);
        $listingRoute = $isDeletedView ? route('admin.reports.smazane', $udalost) : route('admin.reports.prihlasky', $udalost);
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="section-eyebrow">Report přihlášek</p>
                <h1 class="text-3xl text-[#20392c]">{{ $isDeletedView ? 'Smazané přihlášky' : 'Aktivní přihlášky' }}</h1>
                <p class="max-w-3xl text-sm leading-6 text-gray-600">{{ $udalost->nazev }} • přehled registrací, startovních čísel a exportů pro pořadatele.</p>
            </div>
            <a href="{{ route('admin.udalosti.show', $udalost) }}" class="button-secondary">Přehled události</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6">
            @include('admin.udalosti._tabs', ['udalost' => $udalost, 'active' => 'prihlasky'])

            <section class="panel p-5">
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.reports.prihlasky', $udalost) }}" @class([
                        'rounded-full border px-4 py-2 text-sm font-semibold transition',
                        'border-[#20392c] bg-[#20392c] text-white' => ! $isDeletedView,
                        'border-[#ddd0bc] bg-white/70 text-gray-600' => $isDeletedView,
                    ])>Aktivní</a>
                    <a href="{{ route('admin.reports.smazane', $udalost) }}" @class([
                        'rounded-full border px-4 py-2 text-sm font-semibold transition',
                        'border-[#20392c] bg-[#20392c] text-white' => $isDeletedView,
                        'border-[#ddd0bc] bg-white/70 text-gray-600' => ! $isDeletedView,
                    ])>Smazané</a>
                </div>
            </section>

            @if(count($duplicateStartNumbers) > 0)
                <div class="status-note border-amber-200 bg-amber-50 text-amber-900">
                    Duplicitní startovní čísla: {{ implode(', ', $duplicateStartNumbers) }}
                </div>
            @endif

            <section class="panel p-6">
                <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_220px]">
                    <form method="GET" action="{{ $listingRoute }}" class="grid gap-4 md:grid-cols-[minmax(0,1fr)_180px_auto]">
                        <div>
                            <x-input-label for="q" :value="'Hledat podle čísla, osoby, koně nebo e-mailu'" />
                            <x-text-input id="q" name="q" type="text" :value="$filters['q']" />
                        </div>
                        <div>
                            <x-input-label for="stav" :value="'Stav'" />
                            <select id="stav" name="stav" class="field-shell">
                                <option value="active" @selected($filters['stav'] === 'active')>Aktivní</option>
                                <option value="deleted" @selected($filters['stav'] === 'deleted')>Smazané</option>
                                <option value="all" @selected($filters['stav'] === 'all')>Všechny</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-3">
                            <button type="submit" class="button-primary">Filtrovat</button>
                            <a href="{{ $listingRoute }}" class="button-secondary">Reset</a>
                        </div>
                    </form>

                    <div class="flex flex-wrap items-end gap-3 xl:justify-end">
                        @if(! $isDeletedView)
                            <form method="POST" action="{{ route('admin.reports.start-cisla.normalize', $udalost) }}">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="button-secondary">Srovnat startovní čísla</button>
                            </form>
                        @endif
                    </div>
                </div>
            </section>

            <section class="panel p-5">
                <div class="flex flex-wrap gap-3">
                    <a class="button-secondary" href="{{ route('admin.reports.export.seznam', $udalost) }}">Export seznam</a>
                    <a class="button-secondary" href="{{ route('admin.reports.export.discipliny', $udalost) }}">Export disciplíny</a>
                    <a class="button-secondary" href="{{ route('admin.reports.export.emaily', $udalost) }}">Export e-maily</a>
                    <a class="button-secondary" href="{{ route('admin.reports.export.kone', $udalost) }}">Export vet</a>
                    <a class="button-secondary" href="{{ route('admin.reports.export.bulk-pdf', $udalost) }}">Bulk PDF ZIP</a>
                </div>
            </section>

            <section class="panel p-5 text-sm text-gray-700">
                @if($prihlasky->total() > 0)
                    Zobrazeno {{ $prihlasky->firstItem() }}–{{ $prihlasky->lastItem() }} z {{ $prihlasky->total() }} přihlášek.
                @else
                    Zobrazeno 0 z 0 přihlášek.
                @endif
            </section>

            <section class="space-y-4">
                @forelse($prihlasky as $p)
                    <article @class([
                        'panel p-6',
                        'bg-red-50/60' => $p->smazana,
                        'ring-1 ring-amber-300 bg-amber-50/60' => $p->start_cislo !== null && in_array((int) $p->start_cislo, $duplicateStartNumbers, true),
                    ])>
                        <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-3">
                                    <p class="text-xl font-semibold text-[#20392c]">#{{ $p->start_cislo ?? '—' }} • {{ $p->osoba?->prijmeni }} {{ $p->osoba?->jmeno }}{{ $p->vekKategorie() }}</p>
                                    @if($p->smazana)
                                        <span class="brand-pill bg-red-100 text-red-700">Smazaná</span>
                                    @endif
                                </div>
                                <div class="grid gap-2 text-sm text-gray-600 md:grid-cols-2">
                                    <p>Kůň: {{ $p->kun?->jmeno }} @if($p->kunTandem) + {{ $p->kunTandem->jmeno }} @endif</p>
                                    <p>Cena: {{ number_format((float) $p->cena_celkem, 2, ',', ' ') }} Kč</p>
                                    <p>E-mail: {{ $p->user?->email }}</p>
                                    <p>Startovní číslo: {{ $p->start_cislo ?? 'nenastaveno' }}</p>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('admin.reports.start-cislo.update', [$udalost, $p]) }}" class="flex flex-wrap items-end gap-3">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="q" value="{{ $filters['q'] }}">
                                <input type="hidden" name="stav" value="{{ $filters['stav'] }}">
                                <div>
                                    <label for="start_cislo_{{ $p->id }}" class="block text-xs font-semibold uppercase tracking-[0.18em] text-[#7b5230]">Startovní číslo</label>
                                    <input id="start_cislo_{{ $p->id }}" name="start_cislo" type="number" min="1" class="field-shell w-28" value="{{ $p->start_cislo }}">
                                </div>
                                <button type="submit" class="button-primary">Uložit</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="panel p-8 text-sm text-gray-600">Žádné záznamy.</div>
                @endforelse
            </section>

            <div>
                {{ $prihlasky->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
