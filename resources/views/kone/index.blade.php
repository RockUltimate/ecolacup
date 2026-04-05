<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Koně
            </h2>
            <a href="{{ route('kone.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                Nový kůň
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <x-flash-message />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse ($kone as $kun)
                    @php($passportComplete = filled($kun->cislo_prukazu) && filled($kun->cislo_hospodarstvi) && filled($kun->majitel_jmeno_adresa))
                    <div class="panel p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $kun->jmeno }}</h3>
                                <p class="text-sm text-gray-600">
                                    {{ $kun->plemeno_nazev ?: $kun->plemeno_vlastni ?: $kun->plemeno_kod ?: 'Bez plemene' }} • {{ $kun->rok_narozeni }} • {{ $kun->staj }}
                                </p>
                                <div class="mt-2">
                                    <span @class([
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                        'bg-emerald-100 text-emerald-700' => $passportComplete,
                                        'bg-amber-100 text-amber-700' => ! $passportComplete,
                                    ])>
                                        {{ $passportComplete ? 'Průkaz kompletní' : 'Průkaz nekompletní' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('kone.edit', $kun) }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Upravit</a>
                                <form method="POST" action="{{ route('kone.destroy', $kun) }}" onsubmit="return confirm('Opravdu smazat koně?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800 underline">Smazat</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="panel p-6 text-sm text-gray-600">
                        Zatím nemáte žádné koně. Přidejte první záznam.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
