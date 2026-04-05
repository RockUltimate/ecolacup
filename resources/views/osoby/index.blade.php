<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Osoby
            </h2>
            <a href="{{ route('osoby.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
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
                                <p class="font-medium text-gray-900">{{ $osoba->prijmeni }} {{ $osoba->jmeno }}</p>
                                <p class="text-sm text-gray-600">Datum narození: {{ $osoba->datum_narozeni?->format('d.m.Y') }}</p>
                                <p class="text-sm text-gray-600">Stáj: {{ $osoba->staj }}</p>
                                <div class="mt-2">
                                    <x-badge-cmt :status="$osoba->cmt_status" />
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('osoby.edit', $osoba) }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Upravit</a>
                                <form method="POST" action="{{ route('osoby.destroy', $osoba) }}" onsubmit="return confirm('Opravdu smazat osobu?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800 underline">Smazat</button>
                                </form>
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
