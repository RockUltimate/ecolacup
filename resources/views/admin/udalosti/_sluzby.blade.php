<section class="panel p-6 sm:p-8">
    <div class="space-y-6">
        <div>
            <p class="section-eyebrow">Ustájení a služby</p>
            <h2 class="mt-2 text-2xl text-[#20392c]">Přidat novou službu</h2>
        </div>

        <form method="POST" action="{{ route('admin.udalosti.ustajeni.store', $udalost) }}" class="grid gap-4 md:grid-cols-2" enctype="multipart/form-data">
            @csrf
            <input type="text" name="nazev" placeholder="Název položky" class="field-shell" required>
            <select name="typ" class="field-shell" required>
                <option value="">-- Vyberte typ --</option>
                <option value="ustajeni">Ustájení</option>
                <option value="ubytovani">Ubytování</option>
                <option value="strava">Strava</option>
                <option value="ostatni">Ostatní</option>
            </select>
            <input type="number" name="cena" step="0.01" min="0" placeholder="Cena" class="field-shell" required>
            <input type="number" name="kapacita" min="1" placeholder="Kapacita" class="field-shell">

            <div class="md:col-span-2">
                <input type="file" name="foto_path" accept="image/*" placeholder="Fotografie" class="field-shell">
                <p class="mt-1 text-sm text-gray-500">Volitelně: obrázek služby (JPG, PNG, WebP)</p>
            </div>

            <div class="md:col-span-2">
                <input type="file" name="pdf_path" accept=".pdf" placeholder="PDF" class="field-shell">
                <p class="mt-1 text-sm text-gray-500">Volitelně: PDF s informacemi o službě</p>
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="button-primary">Přidat službu</button>
            </div>
        </form>

        <div class="mt-8 space-y-3">
            <h3 class="font-semibold text-[#20392c]">Stávající služby</h3>
            @forelse($udalost->ustajeniMoznosti as $moznost)
                <div class="surface-muted space-y-3 p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="font-semibold text-[#20392c]">{{ $moznost->nazev }}</p>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $moznost->typ }}
                                • {{ number_format((float) $moznost->cena, 2, ',', ' ') }} Kč
                                @if($moznost->kapacita)• kapacita {{ $moznost->kapacita }}@endif
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
                            <a href="{{ route('admin.udalosti.ustajeni.edit', [$udalost, $moznost]) }}" class="text-sm text-[#3d6b4f] underline underline-offset-4">Upravit</a>
                            <form method="POST" action="{{ route('admin.udalosti.ustajeni.destroy', [$udalost, $moznost]) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Opravdu smazat?')" class="text-sm text-red-700 underline underline-offset-4">Smazat</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-600">Zatím bez služeb.</p>
            @endforelse
        </div>
    </div>
</section>
