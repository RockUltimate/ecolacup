<section class="panel p-6 sm:p-8">
    <div class="space-y-6">
        <div>
            <p class="section-eyebrow">Disciplíny</p>
            <h2 class="mt-2 text-2xl text-[#20392c]">Přidat novou disciplínu</h2>
        </div>

        <form method="POST" action="{{ route('admin.udalosti.moznosti.store', $udalost) }}" class="grid gap-4 md:grid-cols-2" enctype="multipart/form-data">
            @csrf
            <input type="text" name="nazev" placeholder="Název disciplíny" class="field-shell" required>
            <input type="number" name="cena" step="0.01" min="0" placeholder="Cena" class="field-shell" required>
            <input type="number" name="min_vek" min="0" placeholder="Min. věk" class="field-shell">
            <input type="number" name="poradi" min="0" placeholder="Pořadí" class="field-shell">

            <div class="md:col-span-2">
                <label for="moznost_create_foto_path" class="flex cursor-pointer items-center justify-between gap-4 rounded-[1.25rem] border border-[#dccdb8] bg-white px-4 py-3 text-sm text-gray-700">
                    <span id="moznost_create_foto_path_label" class="truncate">Žádný soubor nevybrán</span>
                    <span class="rounded-full bg-[#3d6b4f] px-4 py-2 font-semibold text-white">Vyberte obrázek</span>
                </label>
                <input
                    id="moznost_create_foto_path"
                    type="file"
                    name="foto_path"
                    accept="image/*"
                    class="sr-only"
                    onchange="document.getElementById('moznost_create_foto_path_label').textContent = this.files && this.files[0] ? this.files[0].name : 'Žádný soubor nevybrán'"
                >
                <p class="mt-1 text-sm text-gray-500">Volitelně: obrázek disciplíny (JPG, PNG, WebP)</p>
            </div>

            <div class="md:col-span-2">
                <label for="moznost_create_pdf_path" class="flex cursor-pointer items-center justify-between gap-4 rounded-[1.25rem] border border-[#dccdb8] bg-white px-4 py-3 text-sm text-gray-700">
                    <span id="moznost_create_pdf_path_label" class="truncate">Žádný soubor nevybrán</span>
                    <span class="rounded-full bg-[#3d6b4f] px-4 py-2 font-semibold text-white">Vyberte PDF</span>
                </label>
                <input
                    id="moznost_create_pdf_path"
                    type="file"
                    name="pdf_path"
                    accept=".pdf"
                    class="sr-only"
                    onchange="document.getElementById('moznost_create_pdf_path_label').textContent = this.files && this.files[0] ? this.files[0].name : 'Žádný soubor nevybrán'"
                >
                <p class="mt-1 text-sm text-gray-500">Volitelně: PDF s informacemi o disciplíně</p>
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="button-primary">Přidat disciplínu</button>
            </div>
        </form>

        <div class="mt-8 space-y-3">
            <h3 class="font-semibold text-[#20392c]">Stávající disciplíny</h3>
            @forelse($udalost->moznosti as $moznost)
                <div class="surface-muted space-y-3 p-4">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-[#20392c]">{{ $moznost->nazev }}</p>
                                @if($moznost->je_administrativni_poplatek)
                                    <span class="rounded-full bg-[#f0ebe3] px-2 py-0.5 text-xs font-medium text-[#7b5230]">automaticky</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ number_format((float) $moznost->cena, 2, ',', ' ') }} Kč
                                @if($moznost->min_vek)• min. věk {{ $moznost->min_vek }}@endif
                            </p>
                            @if($moznost->foto_path || $moznost->pdf_path)
                                <div class="mt-2 flex gap-2">
                                    @if($moznost->foto_path)
                                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($moznost->foto_path) }}" target="_blank" rel="noopener" class="text-xs text-[#7b5230] underline">Fotografie</a>
                                    @endif
                                    @if($moznost->pdf_path)
                                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($moznost->pdf_path) }}" target="_blank" rel="noopener" class="text-xs text-[#7b5230] underline">PDF</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                        @if(!$moznost->je_administrativni_poplatek)
                        <div class="flex w-[170px] flex-col gap-3">
                            <a href="{{ route('admin.udalosti.moznosti.edit', [$udalost, $moznost]) }}" class="button-primary w-full">Upravit</a>
                            <form method="POST" action="{{ route('admin.udalosti.moznosti.destroy', [$udalost, $moznost]) }}" class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Opravdu smazat?')" class="w-full rounded-full border border-red-200 bg-red-50 px-5 py-3 text-sm font-semibold text-red-700 transition hover:bg-red-100">Smazat</button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-600">Zatím bez disciplín.</p>
            @endforelse
        </div>
    </div>
</section>
