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
                <input type="file" name="foto_path" accept="image/*" placeholder="Fotografie" class="field-shell">
                <p class="mt-1 text-sm text-gray-500">Volitelně: obrázek disciplíny (JPG, PNG, WebP)</p>
            </div>

            <div class="md:col-span-2">
                <input type="file" name="pdf_path" accept=".pdf" placeholder="PDF" class="field-shell">
                <p class="mt-1 text-sm text-gray-500">Volitelně: PDF s informacemi o disciplíně</p>
            </div>

            <label class="md:col-span-2 flex items-center gap-3 rounded-[1rem] border border-[#eadfcc] bg-white/60 px-4 py-3 text-sm text-gray-700">
                <input type="checkbox" name="je_administrativni_poplatek" value="1" class="rounded border-[#ccb28f] text-[#3d6b4f] focus:ring-[#3d6b4f]">
                <span>Administrativní poplatek</span>
            </label>
            <div class="md:col-span-2">
                <button type="submit" class="button-primary">Přidat disciplínu</button>
            </div>
        </form>

        <div class="mt-8 space-y-3">
            <h3 class="font-semibold text-[#20392c]">Stávající disciplíny</h3>
            @forelse($udalost->moznosti as $moznost)
                <div class="surface-muted space-y-3 p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="font-semibold text-[#20392c]">{{ $moznost->nazev }}</p>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ number_format((float) $moznost->cena, 2, ',', ' ') }} Kč
                                @if($moznost->min_vek)• min. věk {{ $moznost->min_vek }}@endif
                            </p>
                            @if($moznost->foto_path || $moznost->pdf_path)
                                <div class="mt-2 flex gap-2">
                                    @if($moznost->foto_path)
                                        <a href="{{ asset('storage/'.$moznost->foto_path) }}" target="_blank" rel="noopener" class="text-xs text-[#7b5230] underline">Fotografie</a>
                                    @endif
                                    @if($moznost->pdf_path)
                                        <a href="{{ asset('storage/'.$moznost->pdf_path) }}" target="_blank" rel="noopener" class="text-xs text-[#7b5230] underline">PDF</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.udalosti.moznosti.edit', [$udalost, $moznost]) }}" class="text-sm text-[#3d6b4f] underline underline-offset-4">Upravit</a>
                            <form method="POST" action="{{ route('admin.udalosti.moznosti.destroy', [$udalost, $moznost]) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Opravdu smazat?')" class="text-sm text-red-700 underline underline-offset-4">Smazat</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-600">Zatím bez disciplín.</p>
            @endforelse
        </div>
    </div>
</section>
