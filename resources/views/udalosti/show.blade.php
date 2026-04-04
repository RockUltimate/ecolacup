<x-site-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $udalost->nazev }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <p class="text-sm text-gray-600">{{ $udalost->misto }}</p>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $udalost->datum_zacatek?->format('d.m.Y') }} - {{ $udalost->datum_konec?->format('d.m.Y') }}
                </p>
                <p class="text-sm text-gray-600 mt-1">Uzávěrka přihlášek: {{ $udalost->uzavierka_prihlasek?->format('d.m.Y') }}</p>
                <p class="text-sm text-gray-600 mt-1">Počet přihlášek: {{ $udalost->pocet_prihlasek }} • Počet startů: {{ $udalost->pocet_startu }}</p>
                <div class="mt-4">
                    @auth
                        <a href="{{ route('prihlasky.create', $udalost) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                            Přihlásit se na akci
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                            Přihlaste se pro registraci
                        </a>
                    @endauth
                </div>
                @if($udalost->popis)
                    <p class="mt-4 text-gray-800 whitespace-pre-line">{{ $udalost->popis }}</p>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow sm:rounded-lg p-6">
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
