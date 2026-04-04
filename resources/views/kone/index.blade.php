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
            @if (session('status'))
                <div class="mb-4 p-4 rounded-md bg-green-50 text-green-700 text-sm">
                    @if (session('status') === 'kun-created') Kůň byl vytvořen. @endif
                    @if (session('status') === 'kun-updated') Kůň byl upraven. @endif
                    @if (session('status') === 'kun-deleted') Kůň byl smazán. @endif
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse ($kone as $kun)
                    @php($ockovani = $kun->ockovaniOk())
                    <div class="bg-white shadow sm:rounded-lg p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $kun->jmeno }}</h3>
                                <p class="text-sm text-gray-600">
                                    {{ $kun->plemeno_kod ?: 'Bez plemene' }} • {{ $kun->rok_narozeni }} • {{ $kun->staj }}
                                </p>
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

                        <div class="mt-4 flex flex-wrap gap-2 text-xs">
                            @foreach (['ehv_datum' => 'EHV', 'aie_datum' => 'AIE', 'chripka_datum' => 'Chřipka'] as $field => $label)
                                @php($state = $ockovani[$field] ?? 'missing')
                                <span @class([
                                    'px-2 py-1 rounded-full',
                                    'bg-green-100 text-green-700' => $state === 'ok',
                                    'bg-amber-100 text-amber-700' => $state === 'expired',
                                    'bg-red-100 text-red-700' => $state === 'missing',
                                ])>
                                    {{ $label }}: {{ $state === 'ok' ? 'OK' : ($state === 'expired' ? 'Po termínu' : 'Chybí') }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="bg-white shadow sm:rounded-lg p-6 text-sm text-gray-600">
                        Zatím nemáte žádné koně. Přidejte první záznam.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
