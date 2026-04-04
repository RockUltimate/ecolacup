<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Přihlášky
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 rounded-md bg-green-50 text-green-700 text-sm">
                    @if (session('status') === 'prihlaska-created') Přihláška byla vytvořena. @endif
                    @if (session('status') === 'prihlaska-deleted') Přihláška byla smazána. @endif
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @forelse($prihlasky as $prihlaska)
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <p class="font-medium text-gray-900">{{ $prihlaska->udalost?->nazev }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $prihlaska->osoba?->prijmeni }} {{ $prihlaska->osoba?->jmeno }} • {{ $prihlaska->kun?->jmeno }}
                                </p>
                                <p class="text-sm text-gray-600">Cena: {{ number_format((float)$prihlaska->cena_celkem, 2, ',', ' ') }} Kč</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('prihlasky.show', $prihlaska) }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Detail</a>
                                <a href="{{ route('prihlasky.pdf', $prihlaska) }}" class="text-sm text-gray-700 hover:text-gray-900 underline">PDF</a>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-sm text-gray-600">Zatím nemáte žádné přihlášky.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
