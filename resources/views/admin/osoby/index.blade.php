<x-app-layout>
    @php
        $filters = $filters ?? ['q' => ''];
    @endphp

    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Admin • Osoby</p>
            <h1 class="text-3xl text-[#20392c]">Všechny osoby</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">Centrální přehled osob napříč účty včetně přiřazeného uživatele a počtu přihlášek.</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6">
            <x-flash-message />

            <x-admin-report-filter-form :action="route('admin.osoby.index')" :reset-href="route('admin.osoby.index')">
                <div>
                    <x-input-label for="q" :value="'Hledat (jméno, příjmení, stáj, uživatel)'" />
                    <x-text-input id="q" name="q" type="text" class="mt-1 block w-full" :value="$filters['q']" />
                </div>
            </x-admin-report-filter-form>

            <section class="panel overflow-hidden">
                <div class="divide-y divide-[#eadfcc]">
                    @forelse($osoby as $osoba)
                        <div class="p-5">
                            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                <div class="space-y-2">
                                    <p class="text-lg font-semibold text-[#20392c]">{{ $osoba->prijmeni }} {{ $osoba->jmeno }}</p>
                                    <p class="text-sm text-gray-600">Datum narození: {{ $osoba->datum_narozeni?->format('d.m.Y') }} • Stáj: {{ $osoba->staj }}</p>
                                    <p class="text-sm text-gray-600">
                                        Uživatel:
                                        @if($osoba->user)
                                            <a href="{{ route('admin.users.edit', $osoba->user) }}" class="text-[#3d6b4f] underline underline-offset-4">{{ $osoba->user->celeJmeno() }}</a>
                                            • {{ $osoba->user->email }}
                                        @else
                                            nepřiřazen
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">Přihlášky: {{ $osoba->prihlasky_count }}</p>
                                </div>

                                <div class="flex w-[170px] max-w-full flex-col gap-3">
                                    <a href="{{ route('admin.osoby.edit', $osoba) }}" class="button-secondary w-full">Upravit</a>
                                    <form method="POST" action="{{ route('admin.osoby.destroy', $osoba) }}" class="w-full" onsubmit="return confirm('Opravdu smazat osobu?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button-secondary w-full border-red-200 bg-red-50 text-red-700 hover:bg-red-100">Smazat</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-sm text-gray-600">Žádné osoby nenalezeny.</div>
                    @endforelse
                </div>
            </section>

            <div>
                {{ $osoby->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
