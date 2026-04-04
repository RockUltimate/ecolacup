<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Upravit událost</h2>
            <div class="text-sm flex items-center gap-3">
                <a href="{{ route('admin.reports.prihlasky', $udalost) }}" class="text-indigo-600 underline">Přihlášky</a>
                <a href="{{ route('admin.reports.startky', $udalost) }}" class="text-indigo-600 underline">Startky</a>
                <a href="{{ route('admin.reports.ubytovani', $udalost) }}" class="text-indigo-600 underline">Ubytování</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 rounded-md bg-green-50 text-green-700 text-sm">
                    @if (session('status') === 'udalost-updated') Událost byla upravena. @endif
                    @if (session('status') === 'moznost-created') Disciplína byla přidána. @endif
                    @if (session('status') === 'moznost-deleted') Disciplína byla smazána. @endif
                    @if (session('status') === 'ustajeni-created') Možnost ustájení byla přidána. @endif
                    @if (session('status') === 'ustajeni-deleted') Možnost ustájení byla smazána. @endif
                </div>
            @endif

            <div class="p-6 bg-white shadow sm:rounded-lg">
                @include('admin.udalosti._form', ['udalost' => $udalost])
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="p-6 bg-white shadow sm:rounded-lg space-y-4">
                    <h3 class="font-semibold text-gray-900">Disciplíny</h3>
                    <form method="POST" action="{{ route('admin.udalosti.moznosti.store', $udalost) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @csrf
                        <input type="text" name="nazev" placeholder="Název" class="border-gray-300 rounded-md" required>
                        <input type="number" name="cena" step="0.01" min="0" placeholder="Cena" class="border-gray-300 rounded-md" required>
                        <input type="number" name="min_vek" min="0" placeholder="Min. věk" class="border-gray-300 rounded-md">
                        <input type="number" name="poradi" min="0" placeholder="Pořadí" class="border-gray-300 rounded-md">
                        <label class="inline-flex items-center text-sm text-gray-700 md:col-span-2">
                            <input type="checkbox" name="je_administrativni_poplatek" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ms-2">Administrativní poplatek</span>
                        </label>
                        <button type="submit" class="md:col-span-2 inline-flex justify-center px-4 py-2 bg-indigo-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-indigo-500">
                            Přidat disciplínu
                        </button>
                    </form>

                    <div class="divide-y divide-gray-200">
                        @forelse($udalost->moznosti as $moznost)
                            <div class="py-2 flex items-center justify-between">
                                <p class="text-sm text-gray-800">{{ $moznost->nazev }} • {{ number_format((float)$moznost->cena, 2, ',', ' ') }} Kč</p>
                                <form method="POST" action="{{ route('admin.udalosti.moznosti.destroy', [$udalost, $moznost]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm text-red-600 hover:text-red-800 underline">Smazat</button>
                                </form>
                            </div>
                        @empty
                            <p class="py-2 text-sm text-gray-600">Zatím bez disciplín.</p>
                        @endforelse
                    </div>
                </div>

                <div class="p-6 bg-white shadow sm:rounded-lg space-y-4">
                    <h3 class="font-semibold text-gray-900">Ustájení / ubytování</h3>
                    <form method="POST" action="{{ route('admin.udalosti.ustajeni.store', $udalost) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @csrf
                        <input type="text" name="nazev" placeholder="Název" class="border-gray-300 rounded-md" required>
                        <select name="typ" class="border-gray-300 rounded-md" required>
                            <option value="ustajeni">Ustájení</option>
                            <option value="ubytovani">Ubytování</option>
                            <option value="strava">Strava</option>
                            <option value="ostatni">Ostatní</option>
                        </select>
                        <input type="number" name="cena" step="0.01" min="0" placeholder="Cena" class="border-gray-300 rounded-md" required>
                        <input type="number" name="kapacita" min="1" placeholder="Kapacita" class="border-gray-300 rounded-md">
                        <button type="submit" class="md:col-span-2 inline-flex justify-center px-4 py-2 bg-indigo-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-indigo-500">
                            Přidat možnost
                        </button>
                    </form>

                    <div class="divide-y divide-gray-200">
                        @forelse($udalost->ustajeniMoznosti as $moznost)
                            <div class="py-2 flex items-center justify-between">
                                <p class="text-sm text-gray-800">{{ $moznost->nazev }} • {{ $moznost->typ }} • {{ number_format((float)$moznost->cena, 2, ',', ' ') }} Kč</p>
                                <form method="POST" action="{{ route('admin.udalosti.ustajeni.destroy', [$udalost, $moznost]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm text-red-600 hover:text-red-800 underline">Smazat</button>
                                </form>
                            </div>
                        @empty
                            <p class="py-2 text-sm text-gray-600">Zatím bez možností ustájení.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
