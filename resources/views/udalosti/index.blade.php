<x-site-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Události
        </h2>
    </x-slot>

    <div class="py-8 space-y-8">
        <section class="panel p-6 md:p-8 bg-gradient-to-br from-[#f7f0e3] via-[#f4ebdc] to-[#eadfcd]">
            <p class="brand-pill">Kalendář akcí CMT</p>
            <h3 class="mt-3 text-2xl md:text-3xl font-semibold text-[#3d6b4f]">Vyberte si událost a registrujte se online</h3>
            <p class="mt-2 text-sm text-gray-700">Aktuální přehled závodů, uzávěrek přihlášek a archiv minulých ročníků.</p>
        </section>

        <section>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Nadcházející akce</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($upcoming as $udalost)
                    @php
                        $daysToDeadline = $udalost->uzavierka_prihlasek?->diffInDays(now()->startOfDay(), false) ?? 0;
                        $deadlineState = 'open';
                        if (($udalost->uzavierka_prihlasek && $udalost->uzavierka_prihlasek->lt(now()->startOfDay())) || ($udalost->kapacita !== null && $udalost->pocet_prihlasek >= $udalost->kapacita)) {
                            $deadlineState = 'closed';
                        } elseif ($daysToDeadline <= 7) {
                            $deadlineState = 'soon';
                        }
                    @endphp
                    <a href="{{ route('udalosti.show', $udalost) }}" class="panel block p-5 hover:shadow-md transition">
                        <div class="flex items-start justify-between gap-3">
                            <h4 class="font-semibold text-gray-900">{{ $udalost->nazev }}</h4>
                            @if($deadlineState === 'closed')
                                <span class="inline-flex rounded-full bg-red-100 text-red-700 text-xs px-2.5 py-1 font-semibold">Uzavřeno</span>
                            @elseif($deadlineState === 'soon')
                                <span class="inline-flex rounded-full bg-amber-100 text-amber-700 text-xs px-2.5 py-1 font-semibold">Uzávěrka brzy</span>
                            @else
                                <span class="inline-flex rounded-full bg-emerald-100 text-emerald-700 text-xs px-2.5 py-1 font-semibold">Otevřeno</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mt-2">{{ $udalost->misto }}</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $udalost->datum_zacatek?->format('d.m.Y') }} - {{ $udalost->datum_konec?->format('d.m.Y') }}</p>
                        <div class="mt-3 flex items-center justify-between text-xs text-gray-600">
                            <span>Uzávěrka: {{ $udalost->uzavierka_prihlasek?->format('d.m.Y') }}</span>
                            <span>Přihlášek: {{ $udalost->pocet_prihlasek }}</span>
                        </div>
                    </a>
                @empty
                    <div class="panel p-5 text-sm text-gray-600">Zatím nejsou vypsané žádné nadcházející akce.</div>
                @endforelse
            </div>
        </section>

        <section>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Archiv</h3>
            <div class="panel overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @forelse($archive as $udalost)
                        <a href="{{ route('udalosti.show', $udalost) }}" class="block p-4 sm:p-5 hover:bg-[#faf6ef] transition">
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
</x-site-layout>
