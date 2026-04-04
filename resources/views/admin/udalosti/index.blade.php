<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin • Události</h2>
            <a href="{{ route('admin.udalosti.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                Nová událost
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 rounded-md bg-green-50 text-green-700 text-sm">
                    @if (session('status') === 'udalost-created') Událost byla vytvořena. @endif
                    @if (session('status') === 'udalost-deleted') Událost byla smazána. @endif
                </div>
            @endif
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @forelse($udalosti as $udalost)
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <p class="font-medium text-gray-900">{{ $udalost->nazev }}</p>
                                <p class="text-sm text-gray-600">{{ $udalost->misto }} • {{ $udalost->datum_zacatek?->format('d.m.Y') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.udalosti.edit', $udalost) }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Upravit</a>
                                <form method="POST" action="{{ route('admin.udalosti.destroy', $udalost) }}" onsubmit="return confirm('Opravdu smazat událost?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800 underline">Smazat</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-sm text-gray-600">Zatím nejsou vytvořené žádné události.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
