<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-on-surface dark:text-[#e5e2dd] leading-tight">
                Členství CMT
            </h2>
            <a href="{{ route('clenstvi-cmt.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                Nové členství
            </a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <x-flash-message />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($clenstvi as $item)
                    @php
                        $membershipNo = str_pad((string) ($item->evidencni_cislo ?: $item->id), 4, '0', STR_PAD_LEFT);
                        $nextYear = (int) $item->rok + 1;
                    @endphp
                    <article class="panel p-5 space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs text-on-surface-variant dark:text-[#c3c8bb]">Členství #{{ $membershipNo }}</p>
                                <h3 class="text-lg font-semibold text-on-surface dark:text-[#e5e2dd]">{{ $item->osoba?->prijmeni }} {{ $item->osoba?->jmeno }}</h3>
                                <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">{{ $item->typ_clenstvi }}</p>
                            </div>
                            @if($item->aktivni)
                                <span class="brand-pill">AKTIVNÍ</span>
                            @else
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-error-container text-on-error-container">NEAKTIVNÍ</span>
                            @endif
                        </div>
                        <div class="text-sm text-on-surface dark:text-[#e5e2dd] space-y-1">
                            <p>Rok: <span class="font-medium">{{ $item->rok }}</span></p>
                            <p>Členský poplatek: <span class="font-medium">{{ number_format((float)$item->cena, 2, ',', ' ') }} Kč</span></p>
                            @if($item->evidencni_cislo)
                                <p>Evidenční číslo: <span class="font-medium">{{ $item->evidencni_cislo }}</span></p>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('clenstvi-cmt.edit', $item) }}" class="text-sm brand-link">Upravit</a>
                            <form method="POST" action="{{ route('clenstvi-cmt.renew', $item) }}">
                                @csrf
                                <button type="submit" class="text-sm text-emerald-700 hover:text-emerald-900 underline">Prodloužit na rok {{ $nextYear }}</button>
                            </form>
                            <form method="POST" action="{{ route('clenstvi-cmt.destroy', $item) }}" onsubmit="return confirm('Opravdu smazat členství?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-error underline underline-offset-4">Smazat</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="panel p-5 text-sm text-on-surface-variant dark:text-[#c3c8bb]">Zatím nemáte žádné CMT členství.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
