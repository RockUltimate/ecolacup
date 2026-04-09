<x-app-layout>
    @php
        $filters = $filters ?? ['q' => '', 'stav' => (($showDeleted ?? false) ? 'deleted' : 'active')];
        $duplicateStartNumbers = $duplicateStartNumbers ?? [];
        $isDeletedView = ($showDeleted ?? false);
        $listingRoute = $isDeletedView ? route('admin.reports.smazane', $udalost) : route('admin.reports.prihlasky', $udalost);
    @endphp

    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Report přihlášek</p>
            <h1 class="text-3xl text-[#20392c]">{{ $isDeletedView ? 'Smazané přihlášky' : 'Aktivní přihlášky' }}</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">{{ $udalost->nazev }} • přehled registrací, startovních čísel a exportů pro pořadatele.</p>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.udalosti.index') }}" class="button-secondary w-full">Zpět na události</a>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6">
            @include('admin.udalosti._tabs', ['udalost' => $udalost, 'active' => 'prihlasky'])

            @if(count($duplicateStartNumbers) > 0)
                <div class="status-note border-amber-200 bg-amber-50 text-amber-900">
                    Duplicitní startovní čísla: {{ implode(', ', $duplicateStartNumbers) }}
                </div>
            @endif

            <section class="panel p-6">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <form method="GET" action="{{ $listingRoute }}" class="flex flex-1 flex-col gap-4 xl:flex-row xl:items-end">
                        <div class="space-y-2 xl:min-w-[200px]">
                            <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-[#7b5230]">Přehled</span>
                            <div class="admin-segmented">
                                <a href="{{ route('admin.reports.prihlasky', $udalost) }}" @class([
                                    'admin-segmented-link',
                                    'admin-segmented-link--active' => ! $isDeletedView,
                                ])>Aktivní</a>
                                <a href="{{ route('admin.reports.smazane', $udalost) }}" @class([
                                    'admin-segmented-link',
                                    'admin-segmented-link--active' => $isDeletedView,
                                ])>Smazané</a>
                            </div>
                        </div>
                        <div>
                            <x-input-label for="q" :value="'Hledat podle čísla, osoby, koně nebo e-mailu'" />
                            <x-text-input id="q" name="q" type="text" :value="$filters['q']" />
                        </div>
                        <div class="xl:w-[180px]">
                            <x-input-label for="stav" :value="'Stav'" />
                            <select id="stav" name="stav" class="field-shell">
                                <option value="active" @selected($filters['stav'] === 'active')>Aktivní</option>
                                <option value="deleted" @selected($filters['stav'] === 'deleted')>Smazané</option>
                                <option value="all" @selected($filters['stav'] === 'all')>Všechny</option>
                            </select>
                        </div>
                        <div class="flex flex-wrap items-end gap-3">
                            <button type="submit" class="button-primary">Filtrovat</button>
                            <a href="{{ $listingRoute }}" class="button-secondary">Reset</a>
                        </div>
                    </form>

                    @if(! $isDeletedView)
                        <form method="POST" action="{{ route('admin.reports.start-cisla.normalize', $udalost) }}" class="xl:flex-shrink-0">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="button-secondary">Srovnat startovní čísla</button>
                        </form>
                    @endif
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

                            <div class="flex w-[170px] flex-col items-start gap-3 xl:items-stretch">
                                <form method="POST" action="{{ route('admin.reports.start-cislo.update', [$udalost, $p]) }}" class="flex w-full flex-col items-start gap-3 xl:items-stretch">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="q" value="{{ $filters['q'] }}">
                                    <input type="hidden" name="stav" value="{{ $filters['stav'] }}">
                                    <div class="w-full">
                                        <label for="start_cislo_{{ $p->id }}" class="block text-xs font-semibold uppercase tracking-[0.18em] text-[#7b5230]">Startovní číslo</label>
                                        <input id="start_cislo_{{ $p->id }}" name="start_cislo" type="number" min="1" class="field-shell w-full" value="{{ $p->start_cislo }}">
                                    </div>
                                    <button type="submit" class="button-primary w-full">Uložit</button>
                                </form>

                                @if(! $p->smazana)
                                    <form method="POST" action="{{ route('admin.reports.prihlasky.destroy', [$udalost, $p]) }}" class="w-full" onsubmit="return confirm('Opravdu smazat přihlášku?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full rounded-full border border-red-200 bg-red-50 px-5 py-3 text-sm font-semibold text-red-700 transition hover:bg-red-100">
                                            Smazat
                                        </button>
                                    </form>
                                @endif
                            </div>
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
