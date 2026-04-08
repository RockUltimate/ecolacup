<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="section-eyebrow">Disciplína</p>
                <h1 class="text-3xl text-[#20392c]">Upravit disciplínu</h1>
                <p class="max-w-3xl text-sm leading-6 text-gray-600">{{ $udalost->nazev }} • aktualizace názvu, ceny, pořadí a příloh disciplíny.</p>
            </div>
            <a href="{{ route('admin.udalosti.edit', $udalost) }}#discipliny" class="button-secondary">Zpět na disciplíny</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl space-y-6">
            <x-flash-message />

            <section class="panel p-6 sm:p-8">
                <form method="POST" action="{{ route('admin.udalosti.moznosti.update', [$udalost, $moznost]) }}" class="grid gap-4 md:grid-cols-2" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nazev" :value="'Název disciplíny'" />
                        <input id="nazev" type="text" name="nazev" value="{{ old('nazev', $moznost->nazev) }}" class="field-shell" required>
                        <x-input-error :messages="$errors->get('nazev')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="cena" :value="'Cena'" />
                        <input id="cena" type="number" name="cena" step="0.01" min="0" value="{{ old('cena', $moznost->cena) }}" class="field-shell" required>
                        <x-input-error :messages="$errors->get('cena')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="min_vek" :value="'Min. věk'" />
                        <input id="min_vek" type="number" name="min_vek" min="0" value="{{ old('min_vek', $moznost->min_vek) }}" class="field-shell">
                        <x-input-error :messages="$errors->get('min_vek')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="poradi" :value="'Pořadí'" />
                        <input id="poradi" type="number" name="poradi" min="0" value="{{ old('poradi', $moznost->poradi) }}" class="field-shell">
                        <x-input-error :messages="$errors->get('poradi')" class="mt-2" />
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
                        @if($moznost->foto_path)
                            <p class="mt-2 text-sm text-gray-600">
                                Aktuální soubor:
                                <a href="{{ asset('storage/'.$moznost->foto_path) }}" target="_blank" rel="noopener" class="text-[#7b5230] underline">zobrazit fotografii</a>
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
                        @if($moznost->pdf_path)
                            <p class="mt-2 text-sm text-gray-600">
                                Aktuální soubor:
                                <a href="{{ asset('storage/'.$moznost->pdf_path) }}" target="_blank" rel="noopener" class="text-[#7b5230] underline">zobrazit PDF</a>
                            </p>
                        @endif
                    </div>

                    <label class="md:col-span-2 flex items-center gap-3 rounded-[1rem] border border-[#eadfcc] bg-white/60 px-4 py-3 text-sm text-gray-700">
                        <input type="checkbox" name="je_administrativni_poplatek" value="1" class="rounded border-[#ccb28f] text-[#3d6b4f] focus:ring-[#3d6b4f]" @checked(old('je_administrativni_poplatek', $moznost->je_administrativni_poplatek))>
                        <span>Administrativní poplatek</span>
                    </label>

                    <div class="md:col-span-2 flex flex-wrap gap-3">
                        <button type="submit" class="button-primary">Uložit disciplínu</button>
                        <a href="{{ route('admin.udalosti.edit', $udalost) }}#discipliny" class="button-secondary">Zrušit</a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
