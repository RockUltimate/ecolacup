<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-on-surface dark:text-[#e5e2dd] leading-tight">
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
                    @php($ockovani = $kun->ockovaniOk())
                    @php($passportComplete = filled($kun->cislo_prukazu) && filled($kun->cislo_hospodarstvi) && filled($kun->majitel_jmeno_adresa))
                    <div class="panel p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-on-surface dark:text-[#e5e2dd]">{{ $kun->jmeno }}</h3>
                                <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">
                                    {{ $kun->plemeno_kod ?: 'Bez plemene' }} • {{ $kun->rok_narozeni }} • {{ $kun->staj }}
                                </p>
                                <div class="mt-2">
                                    <span @class([
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                        'bg-primary-fixed text-on-primary-fixed' => $passportComplete,
                                        'bg-tertiary-fixed text-on-tertiary-fixed' => ! $passportComplete,
                                    ])>
                                        {{ $passportComplete ? 'Průkaz kompletní' : 'Průkaz nekompletní' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('kone.edit', $kun) }}" class="text-sm brand-link">Upravit</a>
                                <form method="POST" action="{{ route('kone.destroy', $kun) }}" onsubmit="return confirm('Opravdu smazat koně?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-error underline underline-offset-4">Smazat</button>
                                </form>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2 text-xs">
                            @foreach (['ehv_datum' => 'EHV', 'aie_datum' => 'AIE', 'chripka_datum' => 'Chřipka'] as $field => $label)
                                @php($state = $ockovani[$field] ?? 'missing')
                                <span @class([
                                    'px-2 py-1 rounded-full',
                                    'bg-primary-fixed text-on-primary-fixed' => $state === 'ok',
                                    'bg-tertiary-fixed text-on-tertiary-fixed' => $state === 'expired',
                                    'bg-error-container text-on-error-container' => $state === 'missing',
                                ])>
                                    {{ $label }}: {{ $state === 'ok' ? 'OK' : ($state === 'expired' ? 'Po termínu' : 'Chybí') }}
                                    @if($kun->{$field})
                                        ({{ $kun->{$field}->format('d.m.Y') }})
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="panel p-6 text-sm text-on-surface-variant dark:text-[#c3c8bb]">
                        Zatím nemáte žádné koně. Přidejte první záznam.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
