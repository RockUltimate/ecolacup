<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="section-eyebrow">Nastavení události</p>
                <h1 class="text-3xl text-[#20392c]">{{ $udalost->nazev }}</h1>
                <p class="max-w-3xl text-sm leading-6 text-gray-600">Úprava základních údajů, disciplín a možností ustájení z jednoho místa.</p>
            </div>
            <a href="{{ route('admin.udalosti.show', $udalost) }}" class="button-secondary">Přehled události</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6">
            @include('admin.udalosti._tabs', ['udalost' => $udalost, 'active' => 'popis'])

            <section class="panel p-6 sm:p-8">
                @include('admin.udalosti._form', ['udalost' => $udalost])
            </section>

            <section class="grid gap-6 lg:grid-cols-2">
                <div class="panel space-y-5 p-6 sm:p-8">
                    <div>
                        <p class="section-eyebrow">Disciplíny</p>
                        <h2 class="mt-2 text-2xl text-[#20392c]">Přidat nebo odebrat</h2>
                    </div>

                    <form method="POST" action="{{ route('admin.udalosti.moznosti.store', $udalost) }}" class="grid gap-4 md:grid-cols-2">
                        @csrf
                        <input type="text" name="nazev" placeholder="Název disciplíny" class="field-shell" required>
                        <input type="number" name="cena" step="0.01" min="0" placeholder="Cena" class="field-shell" required>
                        <input type="number" name="min_vek" min="0" placeholder="Min. věk" class="field-shell">
                        <input type="number" name="poradi" min="0" placeholder="Pořadí" class="field-shell">
                        <label class="md:col-span-2 flex items-center gap-3 rounded-[1rem] border border-[#eadfcc] bg-white/60 px-4 py-3 text-sm text-gray-700">
                            <input type="checkbox" name="je_administrativni_poplatek" value="1" class="rounded border-[#ccb28f] text-[#3d6b4f] focus:ring-[#3d6b4f]">
                            <span>Administrativní poplatek</span>
                        </label>
                        <div class="md:col-span-2">
                            <button type="submit" class="button-primary">Přidat disciplínu</button>
                        </div>
                    </form>

                    <div class="space-y-3">
                        @forelse($udalost->moznosti as $moznost)
                            <div class="surface-muted flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-[#20392c]">{{ $moznost->nazev }}</p>
                                    <p class="mt-1 text-sm text-gray-600">{{ number_format((float) $moznost->cena, 2, ',', ' ') }} Kč @if($moznost->min_vek)• min. věk {{ $moznost->min_vek }}@endif</p>
                                </div>
                                <form method="POST" action="{{ route('admin.udalosti.moznosti.destroy', [$udalost, $moznost]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm text-red-700 underline underline-offset-4">Smazat</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-600">Zatím bez disciplín.</p>
                        @endforelse
                    </div>
                </div>

                <div class="panel space-y-5 p-6 sm:p-8">
                    <div>
                        <p class="section-eyebrow">Ustájení a služby</p>
                        <h2 class="mt-2 text-2xl text-[#20392c]">Přidat nebo odebrat</h2>
                    </div>

                    <form method="POST" action="{{ route('admin.udalosti.ustajeni.store', $udalost) }}" class="grid gap-4 md:grid-cols-2">
                        @csrf
                        <input type="text" name="nazev" placeholder="Název položky" class="field-shell" required>
                        <select name="typ" class="field-shell" required>
                            <option value="ustajeni">Ustájení</option>
                            <option value="ubytovani">Ubytování</option>
                            <option value="strava">Strava</option>
                            <option value="ostatni">Ostatní</option>
                        </select>
                        <input type="number" name="cena" step="0.01" min="0" placeholder="Cena" class="field-shell" required>
                        <input type="number" name="kapacita" min="1" placeholder="Kapacita" class="field-shell">
                        <div class="md:col-span-2">
                            <button type="submit" class="button-primary">Přidat možnost</button>
                        </div>
                    </form>

                    <div class="space-y-3">
                        @forelse($udalost->ustajeniMoznosti as $moznost)
                            <div class="surface-muted flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-[#20392c]">{{ $moznost->nazev }}</p>
                                    <p class="mt-1 text-sm text-gray-600">{{ $moznost->typ }} • {{ number_format((float) $moznost->cena, 2, ',', ' ') }} Kč @if($moznost->kapacita)• kapacita {{ $moznost->kapacita }}@endif</p>
                                </div>
                                <form method="POST" action="{{ route('admin.udalosti.ustajeni.destroy', [$udalost, $moznost]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm text-red-700 underline underline-offset-4">Smazat</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-600">Zatím bez možností ustájení.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
