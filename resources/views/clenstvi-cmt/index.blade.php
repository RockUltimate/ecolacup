<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Členství CMT
            </h2>
            <a href="{{ route('clenstvi-cmt.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                Nové členství
            </a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 rounded-md bg-green-50 text-green-700 text-sm">
                    @if (session('status') === 'clenstvi-created') Členství bylo vytvořeno. @endif
                    @if (session('status') === 'clenstvi-updated') Členství bylo upraveno. @endif
                    @if (session('status') === 'clenstvi-deleted') Členství bylo smazáno. @endif
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @forelse($clenstvi as $item)
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ $item->osoba?->prijmeni }} {{ $item->osoba?->jmeno }} • {{ $item->typ_clenstvi }}
                                </p>
                                <p class="text-sm text-gray-600">Rok: {{ $item->rok }} • Cena: {{ number_format((float)$item->cena, 2, ',', ' ') }} Kč</p>
                                <p class="text-sm text-gray-600">
                                    Status:
                                    @if($item->aktivni)
                                        <span class="text-green-700">Aktivní</span>
                                    @else
                                        <span class="text-gray-500">Neaktivní</span>
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('clenstvi-cmt.edit', $item) }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Upravit</a>
                                <form method="POST" action="{{ route('clenstvi-cmt.destroy', $item) }}" onsubmit="return confirm('Opravdu smazat členství?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800 underline">Smazat</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-sm text-gray-600">Zatím nemáte žádné CMT členství.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
