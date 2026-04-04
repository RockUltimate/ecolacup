@php
    $isEdit = isset($prihlaska);
    $selectedMoznosti = old('moznosti', $isEdit ? $prihlaska->polozky->pluck('moznost_id')->all() : []);
    $selectedUstajeni = old('ustajeni', $isEdit ? $prihlaska->ustajeniChoices->pluck('ustajeni_id')->all() : []);
@endphp
<div
    x-data="{
        step: 1,
        isEdit: @js($isEdit),
        osobaId: '{{ old('osoba_id', $isEdit ? $prihlaska->osoba_id : '') }}',
        kunId: '{{ old('kun_id', $isEdit ? $prihlaska->kun_id : '') }}',
        selectedMoznosti: @js(array_map('intval', $selectedMoznosti)),
        selectedUstajeni: @js(array_map('intval', $selectedUstajeni)),
        clenstvi: { status: 'none', label: 'Bez aktivního členství CMT' },
        ockovani: { ehv_datum: null, aie_datum: null, chripka_datum: null, ockovani: {} },
        moznostiMeta: @js($udalost->moznosti->map(fn($m) => ['id' => (int) $m->id, 'nazev' => $m->nazev, 'cena' => (float) $m->cena])->values()),
        ustajeniMeta: @js($udalost->ustajeniMoznosti->map(fn($u) => ['id' => (int) $u->id, 'nazev' => $u->nazev, 'typ' => $u->typ, 'cena' => (float) $u->cena])->values()),
        totalPrice: 0,
        init() {
            this.recalculateTotal();
            if (this.osobaId) this.fetchClenstviStatus();
            if (this.kunId) this.fetchOckovaniStatus();
            this.$watch('selectedMoznosti', () => this.recalculateTotal());
            this.$watch('selectedUstajeni', () => this.recalculateTotal());
            this.$watch('osobaId', () => this.fetchClenstviStatus());
            this.$watch('kunId', () => this.fetchOckovaniStatus());
        },
        nextStep() {
            if (this.step === 1 && (!this.osobaId || !this.kunId)) return;
            if (this.step === 2 && this.selectedMoznosti.length === 0) return;
            this.step = Math.min(3, this.step + 1);
        },
        prevStep() {
            this.step = Math.max(1, this.step - 1);
        },
        selectedMoznostiItems() {
            return this.moznostiMeta.filter(item => this.selectedMoznosti.map(Number).includes(item.id));
        },
        selectedUstajeniItems() {
            return this.ustajeniMeta.filter(item => this.selectedUstajeni.map(Number).includes(item.id));
        },
        recalculateTotal() {
            const moznosti = this.selectedMoznostiItems().reduce((sum, item) => sum + Number(item.cena), 0);
            const ustajeni = this.selectedUstajeniItems().reduce((sum, item) => sum + Number(item.cena), 0);
            this.totalPrice = moznosti + ustajeni;
        },
        formatPrice(value) {
            return new Intl.NumberFormat('cs-CZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
        },
        async fetchClenstviStatus() {
            if (!this.osobaId) {
                this.clenstvi = { status: 'none', label: 'Bez aktivního členství CMT' };
                return;
            }
            const response = await fetch(`/ajax/osoba/${this.osobaId}/clenstvi-img?udalost={{ $udalost->id }}`);
            if (!response.ok) return;
            this.clenstvi = await response.json();
        },
        async fetchOckovaniStatus() {
            if (!this.kunId) {
                this.ockovani = { ehv_datum: null, aie_datum: null, chripka_datum: null, ockovani: {} };
                return;
            }
            const response = await fetch(`/ajax/kun/${this.kunId}/ockovani`);
            if (!response.ok) return;
            this.ockovani = await response.json();
        }
    }"
    x-init="init()"
    class="space-y-6"
>
    <form method="POST" action="{{ $isEdit ? route('prihlasky.update', $prihlaska) : route('prihlasky.store', $udalost) }}" class="space-y-6 pb-24">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <span class="brand-pill" :class="step >= 1 ? '' : 'opacity-50'">1. Osoba a kůň</span>
            <span class="brand-pill" :class="step >= 2 ? '' : 'opacity-50'">2. Výběr položek</span>
            <span class="brand-pill" :class="step >= 3 ? '' : 'opacity-50'">3. Souhrn a odeslání</span>
        </div>

        <section x-show="step === 1" class="panel p-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="osoba_id" :value="'Osoba'" />
                    <select id="osoba_id" x-model="osobaId" name="osoba_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" {{ $isEdit ? 'disabled' : '' }} required>
                        <option value="">Vyberte osobu</option>
                        @foreach($osoby as $osoba)
                            <option value="{{ $osoba->id }}" @selected((int) old('osoba_id', $isEdit ? $prihlaska->osoba_id : 0) === $osoba->id)>
                                {{ $osoba->prijmeni }} {{ $osoba->jmeno }} ({{ $osoba->datum_narozeni?->format('d.m.Y') }})
                            </option>
                        @endforeach
                    </select>
                    @if($isEdit)
                        <input type="hidden" name="osoba_id" value="{{ $prihlaska->osoba_id }}">
                    @endif
                    <x-input-error :messages="$errors->get('osoba_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="kun_id" :value="'Kůň'" />
                    <select id="kun_id" x-model="kunId" name="kun_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" {{ $isEdit ? 'disabled' : '' }} required>
                        <option value="">Vyberte koně</option>
                        @foreach($kone as $kun)
                            <option value="{{ $kun->id }}" @selected((int) old('kun_id', $isEdit ? $prihlaska->kun_id : 0) === $kun->id)>
                                {{ $kun->jmeno }} ({{ $kun->plemeno_kod ?: 'bez plemene' }})
                            </option>
                        @endforeach
                    </select>
                    @if($isEdit)
                        <input type="hidden" name="kun_id" value="{{ $prihlaska->kun_id }}">
                    @endif
                    <x-input-error :messages="$errors->get('kun_id')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="kun_tandem_id" :value="'Tandem kůň (volitelně)'" />
                <select id="kun_tandem_id" name="kun_tandem_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">Bez tandemu</option>
                    @foreach($kone as $kun)
                        <option value="{{ $kun->id }}" @selected((int) old('kun_tandem_id', $isEdit ? ($prihlaska->kun_tandem_id ?? 0) : 0) === $kun->id)>
                            {{ $kun->jmeno }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('kun_tandem_id')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="rounded-lg border border-gray-200 p-3">
                    <p class="font-semibold text-gray-900">Status CMT</p>
                    <p class="mt-1 text-gray-700" x-text="clenstvi.label"></p>
                </div>
                <div class="rounded-lg border border-gray-200 p-3">
                    <p class="font-semibold text-gray-900">Očkování / vyšetření</p>
                    <ul class="mt-1 text-gray-700 space-y-1">
                        <li>EHV: <span x-text="ockovani.ehv_datum ?? 'neuvedeno'"></span></li>
                        <li>AIE: <span x-text="ockovani.aie_datum ?? 'neuvedeno'"></span></li>
                        <li>Chřipka: <span x-text="ockovani.chripka_datum ?? 'neuvedeno'"></span></li>
                    </ul>
                </div>
            </div>
        </section>

        <section x-show="step === 2" class="panel p-5 space-y-5">
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Disciplíny</h3>
                <div class="space-y-2">
                    @foreach($udalost->moznosti as $moznost)
                        <label class="inline-flex items-center w-full p-3 border border-gray-200 rounded-md hover:bg-[#faf6ef] transition">
                            <input type="checkbox" x-model="selectedMoznosti" name="moznosti[]" value="{{ $moznost->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                @checked(in_array($moznost->id, array_map('intval', $selectedMoznosti), true))>
                            <span class="ms-2 text-sm text-gray-700">{{ $moznost->nazev }} — {{ number_format((float)$moznost->cena, 2, ',', ' ') }} Kč</span>
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('moznosti')" class="mt-2" />
            </div>

            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Ustájení / ubytování</h3>
                <div class="space-y-2">
                    @foreach($udalost->ustajeniMoznosti as $item)
                        <label class="inline-flex items-center w-full p-3 border border-gray-200 rounded-md hover:bg-[#faf6ef] transition">
                            <input type="checkbox" x-model="selectedUstajeni" name="ustajeni[]" value="{{ $item->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                @checked(in_array($item->id, array_map('intval', $selectedUstajeni), true))>
                            <span class="ms-2 text-sm text-gray-700">{{ $item->nazev }} ({{ $item->typ }}) — {{ number_format((float)$item->cena, 2, ',', ' ') }} Kč</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </section>

        <section x-show="step === 3" class="panel p-5 space-y-5">
            <h3 class="font-semibold text-gray-900">Souhrn přihlášky</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-semibold text-gray-900">Vybrané disciplíny</p>
                    <ul class="mt-2 space-y-1 text-sm text-gray-700">
                        <template x-for="item in selectedMoznostiItems()" :key="item.id">
                            <li x-text="`${item.nazev} — ${formatPrice(item.cena)} Kč`"></li>
                        </template>
                    </ul>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">Ustájení / ubytování</p>
                    <ul class="mt-2 space-y-1 text-sm text-gray-700">
                        <template x-for="item in selectedUstajeniItems()" :key="`u-${item.id}`">
                            <li x-text="`${item.nazev} (${item.typ}) — ${formatPrice(item.cena)} Kč`"></li>
                        </template>
                    </ul>
                </div>
            </div>
            <div class="rounded-lg border border-[#e5dbc8] bg-[#faf6ef] p-3">
                <p class="text-sm text-gray-700">Celkem k úhradě</p>
                <p class="text-xl font-semibold text-[#3d6b4f]" x-text="`${formatPrice(totalPrice)} Kč`"></p>
            </div>

            <div>
                <x-input-label for="poznamka" :value="'Poznámka'" />
                <textarea id="poznamka" name="poznamka" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('poznamka', $isEdit ? $prihlaska->poznamka : '') }}</textarea>
                <x-input-error :messages="$errors->get('poznamka')" class="mt-2" />
            </div>

            <div>
                <label for="gdpr_souhlas" class="inline-flex items-center">
                    <input id="gdpr_souhlas" type="checkbox" name="gdpr_souhlas" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('gdpr_souhlas', true)) required>
                    <span class="ms-2 text-sm text-gray-700">Souhlasím se zpracováním osobních údajů (GDPR)</span>
                </label>
                <x-input-error :messages="$errors->get('gdpr_souhlas')" class="mt-2" />
            </div>
        </section>

        <div class="flex items-center justify-between gap-3">
            <button type="button" @click="prevStep()" class="inline-flex items-center px-4 py-2 rounded-md border border-gray-300 text-xs font-semibold uppercase tracking-widest text-gray-700 hover:bg-gray-50" x-show="step > 1">
                Zpět
            </button>
            <div class="flex items-center gap-3 ms-auto">
                <button type="button" @click="nextStep()" class="inline-flex items-center px-4 py-2 rounded-md bg-[#3d6b4f] text-xs font-semibold uppercase tracking-widest text-white hover:opacity-90" x-show="step < 3">
                    Pokračovat
                </button>
                <x-primary-button x-show="step === 3">{{ $isEdit ? 'Uložit přihlášku' : 'Odeslat přihlášku' }}</x-primary-button>
                <a href="{{ $isEdit ? route('prihlasky.show', $prihlaska) : route('udalosti.show', $udalost) }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Zpět na detail</a>
            </div>
        </div>
    </form>

    <div class="fixed bottom-3 left-1/2 -translate-x-1/2 w-[calc(100%-1.5rem)] max-w-3xl panel px-4 py-3 flex items-center justify-between gap-4">
        <div class="text-sm text-gray-700">
            <span class="font-semibold">Vybrané položky:</span>
            <span x-text="selectedMoznosti.length + selectedUstajeni.length"></span>
        </div>
        <div class="text-sm text-gray-700">
            <span class="font-semibold">Celkem:</span>
            <span class="text-[#3d6b4f] font-semibold" x-text="`${formatPrice(totalPrice)} Kč`"></span>
        </div>
        <button type="button" class="inline-flex items-center px-3 py-2 rounded-md bg-[#3d6b4f] text-xs font-semibold uppercase tracking-widest text-white hover:opacity-90" @click="step = 3">
            Souhrn
        </button>
        <a href="{{ $isEdit ? route('prihlasky.show', $prihlaska) : route('udalosti.show', $udalost) }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Zpět</a>
    </div>
</div>
