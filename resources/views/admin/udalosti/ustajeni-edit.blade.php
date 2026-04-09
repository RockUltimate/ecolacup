<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Služba</p>
            <h1 class="text-3xl text-[#20392c]">Upravit službu</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">{{ $udalost->nazev }} • aktualizace typu, kapacity, ceny a příloh služby.</p>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.udalosti.edit', $udalost) }}#sluzby" class="button-secondary w-full">Zpět na služby</a>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl space-y-6">
            <x-flash-message />

            <section class="panel p-6 sm:p-8">
                <form method="POST" action="{{ route('admin.udalosti.ustajeni.update', [$udalost, $ustajeni]) }}" class="grid gap-4 md:grid-cols-2" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nazev" :value="'Název položky'" />
                        <input id="nazev" type="text" name="nazev" value="{{ old('nazev', $ustajeni->nazev) }}" class="field-shell" required>
                        <x-input-error :messages="$errors->get('nazev')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="typ" :value="'Typ'" />
                        <select id="typ" name="typ" class="field-shell" required>
                            <option value="">-- Vyberte typ --</option>
                            @foreach(['ustajeni' => 'Ustájení', 'ubytovani' => 'Ubytování', 'strava' => 'Strava', 'ostatni' => 'Ostatní'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('typ', $ustajeni->typ) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('typ')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="cena" :value="'Cena'" />
                        <input id="cena" type="number" name="cena" step="0.01" min="0" value="{{ old('cena', $ustajeni->cena) }}" class="field-shell" required>
                        <x-input-error :messages="$errors->get('cena')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="kapacita" :value="'Kapacita'" />
                        <input id="kapacita" type="number" name="kapacita" min="1" value="{{ old('kapacita', $ustajeni->kapacita) }}" class="field-shell">
                        <x-input-error :messages="$errors->get('kapacita')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="foto_path" :value="'Fotografie'" />
                        <label for="foto_path" class="mt-1 flex cursor-pointer items-center justify-between gap-4 rounded-[1.25rem] border border-[#dccdb8] bg-white px-4 py-3 text-sm text-gray-700">
                            <span id="foto_path_label" class="truncate">Žádný soubor nevybrán</span>
                            <span class="rounded-full bg-[#3d6b4f] px-4 py-2 font-semibold text-white">Vyberte obrázek</span>
                        </label>
                        <input
                            id="foto_path"
                            type="file"
                            name="foto_path"
                            accept="image/*"
                            class="sr-only"
                            onchange="document.getElementById('foto_path_label').textContent = this.files && this.files[0] ? this.files[0].name : 'Žádný soubor nevybrán'"
                        >
                        <x-input-error :messages="$errors->get('foto_path')" class="mt-2" />
                        @if($ustajeni->foto_path)
                            <p class="mt-2 text-sm text-gray-600">
                                Aktuální soubor:
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($ustajeni->foto_path) }}" target="_blank" rel="noopener" class="text-[#7b5230] underline">zobrazit fotografii</a>
                            </p>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="pdf_path" :value="'PDF'" />
                        <label for="pdf_path" class="mt-1 flex cursor-pointer items-center justify-between gap-4 rounded-[1.25rem] border border-[#dccdb8] bg-white px-4 py-3 text-sm text-gray-700">
                            <span id="pdf_path_label" class="truncate">Žádný soubor nevybrán</span>
                            <span class="rounded-full bg-[#3d6b4f] px-4 py-2 font-semibold text-white">Vyberte PDF</span>
                        </label>
                        <input
                            id="pdf_path"
                            type="file"
                            name="pdf_path"
                            accept=".pdf"
                            class="sr-only"
                            onchange="document.getElementById('pdf_path_label').textContent = this.files && this.files[0] ? this.files[0].name : 'Žádný soubor nevybrán'"
                        >
                        <x-input-error :messages="$errors->get('pdf_path')" class="mt-2" />
                        @if($ustajeni->pdf_path)
                            <p class="mt-2 text-sm text-gray-600">
                                Aktuální soubor:
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($ustajeni->pdf_path) }}" target="_blank" rel="noopener" class="text-[#7b5230] underline">zobrazit PDF</a>
                            </p>
                        @endif
                    </div>

                    <div class="md:col-span-2 flex flex-wrap gap-3">
                        <button type="submit" class="button-primary">Uložit službu</button>
                        <a href="{{ route('admin.udalosti.edit', $udalost) }}#sluzby" class="button-secondary">Zrušit</a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
