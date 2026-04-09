<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Moje osoby</p>
            <h1 class="text-3xl text-[#20392c]">Přehled jezdců a účastníků</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">Spravujte osoby, které používáte při registracích na jednotlivé události.</p>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('osoby.create') }}" class="button-primary w-full">Nová osoba</a>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl">
            <x-flash-message />

            <div class="panel overflow-hidden">
                <div class="divide-y divide-[#eadfcc]">
                    @forelse ($osoby as $osoba)
                        <div class="p-5">
                            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                <div class="space-y-2">
                                    <p class="text-lg font-semibold text-[#20392c]">{{ $osoba->prijmeni }} {{ $osoba->jmeno }}</p>
                                <p class="text-sm text-gray-600">Datum narození: {{ $osoba->datum_narozeni?->format('d.m.Y') }}</p>
                                <p class="text-sm text-gray-600">Stáj: {{ $osoba->staj }}</p>
                                </div>
                                <div class="flex w-[170px] max-w-full flex-col gap-3">
                                    <a href="{{ route('osoby.edit', $osoba) }}" class="button-secondary w-full">Upravit</a>
                                    <form method="POST" action="{{ route('osoby.destroy', $osoba) }}" class="w-full" onsubmit="return confirm('Opravdu smazat osobu?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button-secondary w-full border-red-200 bg-red-50 text-red-700 hover:bg-red-100">Smazat</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-sm text-gray-600">
                            Zatím nemáte žádné osoby. Přidejte první záznam.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
