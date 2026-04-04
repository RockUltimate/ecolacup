<x-site-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Události
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <section>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Nadcházející akce</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($upcoming as $udalost)
                        <a href="{{ route('udalosti.show', $udalost) }}" class="block bg-white shadow sm:rounded-lg p-5 hover:shadow-md transition">
                            <h4 class="font-semibold text-gray-900">{{ $udalost->nazev }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $udalost->misto }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $udalost->datum_zacatek?->format('d.m.Y') }} - {{ $udalost->datum_konec?->format('d.m.Y') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-2">Uzávěrka: {{ $udalost->uzavierka_prihlasek?->format('d.m.Y') }}</p>
                        </a>
                    @empty
                        <div class="bg-white shadow sm:rounded-lg p-5 text-sm text-gray-600">
                            Zatím nejsou vypsané žádné nadcházející akce.
                        </div>
                    @endforelse
                </div>
            </section>

            <section>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Archiv</h3>
                <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                    <div class="divide-y divide-gray-200">
                        @forelse($archive as $udalost)
                            <a href="{{ route('udalosti.show', $udalost) }}" class="block p-4 sm:p-5 hover:bg-gray-50 transition">
                                <p class="font-medium text-gray-900">{{ $udalost->nazev }}</p>
                                <p class="text-sm text-gray-600">{{ $udalost->misto }} • {{ $udalost->datum_zacatek?->format('d.m.Y') }}</p>
                            </a>
                        @empty
                            <div class="p-5 text-sm text-gray-600">Archiv je prázdný.</div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-site-layout>
