<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-on-surface dark:text-[#e5e2dd] leading-tight">
                Osoby
            </h2>
            <a href="{{ route('osoby.create') }}" class="button-primary">
                Nová osoba
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <x-flash-message />

            <div class="panel overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @forelse ($osoby as $osoba)
                        <div class="p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <p class="font-medium text-on-surface dark:text-[#e5e2dd]">{{ $osoba->prijmeni }} {{ $osoba->jmeno }}</p>
                                <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">Datum narození: {{ $osoba->datum_narozeni?->format('d.m.Y') }}</p>
                                <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">Stáj: {{ $osoba->staj }}</p>
                                <div class="mt-2">
                                    <x-badge-cmt :status="$osoba->cmt_status" />
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('osoby.edit', $osoba) }}" class="text-sm brand-link">Upravit</a>
                                <form method="POST" action="{{ route('osoby.destroy', $osoba) }}" onsubmit="return confirm('Opravdu smazat osobu?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-error underline underline-offset-4">Smazat</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-sm text-on-surface-variant dark:text-[#c3c8bb]">
                            Zatím nemáte žádné osoby. Přidejte první záznam.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
