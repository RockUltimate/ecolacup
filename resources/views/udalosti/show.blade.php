<x-site-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $udalost->nazev }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @php
                $deadlinePassed = $udalost->uzavierka_prihlasek && $udalost->uzavierka_prihlasek->lt(now()->startOfDay());
                $capacityReached = $udalost->kapacita !== null && $udalost->pocet_prihlasek >= $udalost->kapacita;
                $isClosed = $deadlinePassed || $capacityReached;
                $capacityPercent = $udalost->kapacita ? min(100, (int) round(($udalost->pocet_prihlasek / $udalost->kapacita) * 100)) : null;
                $daysLeft = $udalost->uzavierka_prihlasek ? now()->startOfDay()->diffInDays($udalost->uzavierka_prihlasek, false) : null;
                $capacityBarClass = 'w-0';
                if ($capacityPercent !== null) {
                    $capacityBarClass = match (true) {
                        $capacityPercent >= 100 => 'w-full',
                        $capacityPercent >= 80 => 'w-4/5',
                        $capacityPercent >= 60 => 'w-3/5',
                        $capacityPercent >= 40 => 'w-2/5',
                        $capacityPercent >= 20 => 'w-1/5',
                        default => 'w-[10%]',
                    };
                }
            @endphp
            <div class="panel p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-gray-600">{{ $udalost->misto }}</p>
                    @if($isClosed)
                        <span class="inline-flex rounded-full bg-red-100 text-red-700 text-xs px-3 py-1 font-semibold">Registrace uzavřena</span>
                    @elseif($daysLeft !== null && $daysLeft <= 7)
                        <span class="inline-flex rounded-full bg-amber-100 text-amber-700 text-xs px-3 py-1 font-semibold">Uzávěrka brzy</span>
                    @else
                        <span class="inline-flex rounded-full bg-emerald-100 text-emerald-700 text-xs px-3 py-1 font-semibold">Registrace otevřena</span>
                    @endif
                </div>
                <p class="text-sm text-gray-700 mt-1">{{ $udalost->datum_zacatek?->format('d.m.Y') }} - {{ $udalost->datum_konec?->format('d.m.Y') }}</p>
                <p class="text-sm text-gray-600 mt-1">Uzávěrka přihlášek: {{ $udalost->uzavierka_prihlasek?->format('d.m.Y') }}</p>
                <p class="text-sm text-gray-600 mt-1">Počet přihlášek: {{ $udalost->pocet_prihlasek }} • Počet startů: {{ $udalost->pocet_startu }}</p>
                @if($capacityPercent !== null)
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-xs text-gray-600">
                            <span>Využití kapacity</span>
                            <span>{{ $udalost->pocet_prihlasek }} / {{ $udalost->kapacita }}</span>
                        </div>
                        <div class="mt-1 h-2 rounded-full bg-[#ede2d4] overflow-hidden">
                            <div class="h-2 bg-[#3d6b4f] {{ $capacityBarClass }}"></div>
                        </div>
                    </div>
                @endif
                <div class="mt-5">
                    @auth
                        @if($isClosed)
                            <span class="inline-flex items-center px-4 py-2 bg-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest">
                                Přihlášky uzavřeny
                            </span>
                        @else
                            <a href="{{ route('prihlasky.create', $udalost) }}" class="inline-flex items-center px-4 py-2 bg-[#3d6b4f] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:opacity-90">
                                Přihlásit se na akci
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-[#3d6b4f] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:opacity-90">
                            Přihlaste se pro registraci
                        </a>
                    @endauth
                </div>
                @if($udalost->popis)
                    <p class="mt-4 text-gray-800 whitespace-pre-line">{{ $udalost->popis }}</p>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="panel p-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Disciplíny</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b">
                                    <th class="py-2 pe-2">Název</th>
                                    <th class="py-2 pe-2">Min. věk</th>
                                    <th class="py-2 text-right">Cena</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($udalost->moznosti as $moznost)
                                    <tr class="border-b last:border-b-0">
                                        <td class="py-2 pe-2">{{ $moznost->nazev }}</td>
                                        <td class="py-2 pe-2">{{ $moznost->min_vek ?? '—' }}</td>
                                        <td class="py-2 text-right">{{ number_format((float)$moznost->cena, 2, ',', ' ') }} Kč</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-3 text-gray-600">Zatím nejsou přidané žádné disciplíny.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Ustájení / ubytování</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b">
                                    <th class="py-2 pe-2">Název</th>
                                    <th class="py-2 pe-2">Typ</th>
                                    <th class="py-2 pe-2">Kapacita</th>
                                    <th class="py-2 text-right">Cena</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($udalost->ustajeniMoznosti as $moznost)
                                    <tr class="border-b last:border-b-0">
                                        <td class="py-2 pe-2">{{ $moznost->nazev }}</td>
                                        <td class="py-2 pe-2">{{ $moznost->typ }}</td>
                                        <td class="py-2 pe-2">{{ $moznost->kapacita ?? '—' }}</td>
                                        <td class="py-2 text-right">{{ number_format((float)$moznost->cena, 2, ',', ' ') }} Kč</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-3 text-gray-600">Zatím nejsou přidané žádné možnosti ustájení.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-site-layout>
