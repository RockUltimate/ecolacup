<x-app-layout>
    @php
        $filters = $filters ?? ['q' => ''];
        $duplicateGroups = $duplicateGroups ?? collect();
        $duplicateKeys = $duplicateKeys ?? [];
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="section-eyebrow">Admin • Koně</p>
                <h1 class="text-3xl text-[#20392c]">Všichni koně</h1>
                <p class="max-w-3xl text-sm leading-6 text-gray-600">Centrální přehled koní napříč účty včetně duplicitních jmen a jejich vlastníků.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="button-secondary">Dashboard</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6">
            <x-flash-message />

            <x-admin-report-filter-form :action="route('admin.kone.index')" :reset-href="route('admin.kone.index')">
                <div>
                    <x-input-label for="q" :value="'Hledat (kůň, plemeno, stáj, průkaz, uživatel)'" />
                    <x-text-input id="q" name="q" type="text" class="mt-1 block w-full" :value="$filters['q']" />
                </div>
            </x-admin-report-filter-form>

            <section class="panel p-6 sm:p-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="section-eyebrow">Duplicitní koně</p>
                        <h2 class="mt-2 text-2xl text-[#20392c]">Stejné jméno u více účtů</h2>
                    </div>
                    <span class="brand-pill">{{ $duplicateGroups->count() }} skupin</span>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse($duplicateGroups as $group)
                        @php($sample = $group->first())
                        <div class="surface-muted space-y-4 p-5">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-[#20392c]">{{ $sample->jmeno }}</h3>
                                    <p class="text-sm text-gray-600">{{ $group->count() }} instancí se stejným jménem. Vyberte správný popis a použijte ho pro všechny.</p>
                                </div>
                                <span class="rounded-full border border-[#ddd0bc] bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-[#7b5230]">Duplicita</span>
                            </div>

                            <div class="grid gap-4 lg:grid-cols-2">
                                @foreach($group as $kun)
                                    <article class="rounded-[1.5rem] border border-[#eadfcc] bg-white/80 p-4">
                                        <div class="flex flex-col gap-3">
                                            <div>
                                                <p class="font-semibold text-[#20392c]">{{ $kun->jmeno }}</p>
                                                <p class="text-sm text-gray-600">{{ $kun->plemeno_nazev ?: $kun->plemeno_vlastni ?: $kun->plemeno_kod ?: 'Bez plemene' }} • {{ $kun->rok_narozeni }} • {{ strtoupper($kun->pohlavi) }}</p>
                                                <p class="mt-1 text-sm text-gray-600">Stáj: {{ $kun->staj }}</p>
                                                <p class="mt-1 text-sm text-gray-600">
                                                    Uživatel:
                                                    @if($kun->user)
                                                        <a href="{{ route('admin.users.edit', $kun->user) }}" class="text-[#3d6b4f] underline underline-offset-4">{{ $kun->user->celeJmeno() }}</a>
                                                        • {{ $kun->user->email }}
                                                    @else
                                                        nepřiřazen
                                                    @endif
                                                </p>
                                            </div>

                                            <div class="text-sm text-gray-600">
                                                <p>Průkaz: {{ $kun->cislo_prukazu ?: 'neuvedeno' }}</p>
                                                <p>Hospodářství: {{ $kun->cislo_hospodarstvi ?: 'neuvedeno' }}</p>
                                                <p>Majitel: {{ $kun->majitel_jmeno_adresa ?: 'neuvedeno' }}</p>
                                            </div>

                                            <div class="flex flex-wrap gap-3">
                                                <a href="{{ route('admin.kone.edit', $kun) }}" class="button-secondary">Upravit</a>
                                                <form method="POST" action="{{ route('admin.kone.duplicates.sync') }}" onsubmit="return confirm('Použít tento popis pro všechny koně se stejným jménem?');">
                                                    @csrf
                                                    <input type="hidden" name="source_kun_id" value="{{ $kun->id }}">
                                                    <button type="submit" class="button-primary">Použít pro všechny</button>
                                                </form>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600">Žádné duplicitní skupiny koní nebyly nalezeny.</p>
                    @endforelse
                </div>
            </section>

            <section class="panel overflow-hidden">
                <div class="divide-y divide-[#eadfcc]">
                    @forelse($kone as $kun)
                        @php($isDuplicate = in_array((string) \Illuminate\Support\Str::of($kun->jmeno)->trim()->lower(), $duplicateKeys, true))
                        <div class="p-5">
                            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <p class="text-lg font-semibold text-[#20392c]">{{ $kun->jmeno }}</p>
                                        @if($isDuplicate)
                                            <span class="brand-pill bg-amber-100 text-amber-800">Duplicitní jméno</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $kun->plemeno_nazev ?: $kun->plemeno_vlastni ?: $kun->plemeno_kod ?: 'Bez plemene' }} • {{ $kun->rok_narozeni }} • {{ strtoupper($kun->pohlavi) }} • {{ $kun->staj }}</p>
                                    <p class="text-sm text-gray-600">
                                        Uživatel:
                                        @if($kun->user)
                                            <a href="{{ route('admin.users.edit', $kun->user) }}" class="text-[#3d6b4f] underline underline-offset-4">{{ $kun->user->celeJmeno() }}</a>
                                            • {{ $kun->user->email }}
                                        @else
                                            nepřiřazen
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">Přihlášky: {{ $kun->prihlasky_count }}</p>
                                </div>

                                <a href="{{ route('admin.kone.edit', $kun) }}" class="button-secondary">Upravit</a>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-sm text-gray-600">Žádní koně nenalezeni.</div>
                    @endforelse
                </div>
            </section>

            <div>
                {{ $kone->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
