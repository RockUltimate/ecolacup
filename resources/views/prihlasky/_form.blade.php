@php
    $adminFeeOptions = $udalost->moznosti->filter(fn ($moznost) => (bool) $moznost->je_administrativni_poplatek)->values();
    $adminFeeOption = $adminFeeOptions->first();
    $adminFeeOptionIds = $adminFeeOptions->pluck('id')->map(fn ($id) => (int) $id)->all();
    $selectableMoznosti = $udalost->moznosti->reject(fn ($moznost) => (bool) $moznost->je_administrativni_poplatek)->values();
    $isEdit = isset($prihlaska);
    $selectedMoznosti = array_values(array_filter(
        old('moznosti', $isEdit ? $prihlaska->polozky->pluck('moznost_id')->all() : []),
        fn ($id) => ! in_array((int) $id, $adminFeeOptionIds, true)
    ));
    $selectedUstajeni = old('ustajeni', $isEdit ? $prihlaska->ustajeniChoices->pluck('ustajeni_id')->all() : []);
    $initialAdminFeeApplied = $isEdit && $adminFeeOption && $prihlaska->polozky->contains(
        fn ($item) => in_array((int) $item->moznost_id, $adminFeeOptionIds, true)
    );
@endphp

<div
    x-data="{
        step: 1,
        isEdit: @js($isEdit),
        eventId: @js((int) $udalost->id),
        ignorePrihlaskaId: @js($isEdit ? (int) $prihlaska->id : null),
        osobaId: '{{ old('osoba_id', $isEdit ? $prihlaska->osoba_id : '') }}',
        kunId: '{{ old('kun_id', $isEdit ? $prihlaska->kun_id : '') }}',
        selectedMoznosti: @js(array_map('intval', $selectedMoznosti)),
        selectedUstajeni: @js(array_map('intval', $selectedUstajeni)),
        moznostiMeta: @js($selectableMoznosti->map(fn ($m) => ['id' => (int) $m->id, 'nazev' => $m->nazev, 'cena' => (float) $m->cena])->values()),
        adminFeeMeta: @js($adminFeeOption ? ['id' => (int) $adminFeeOption->id, 'nazev' => $adminFeeOption->nazev, 'cena' => (float) $adminFeeOption->cena] : null),
        ustajeniMeta: @js($udalost->ustajeniMoznosti->map(fn ($u) => ['id' => (int) $u->id, 'nazev' => $u->nazev, 'typ' => $u->typ, 'cena' => (float) $u->cena])->values()),
        adminFeeApplied: @js($initialAdminFeeApplied),
        totalPrice: 0,
        init() {
            this.recalculateTotal();
            this.$watch('selectedMoznosti', () => this.recalculateTotal());
            this.$watch('selectedUstajeni', () => this.recalculateTotal());
            this.$watch('osobaId', () => this.refreshAdminFeeState());
            this.refreshAdminFeeState();
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
        adminFeeOption() {
            return this.adminFeeMeta;
        },
        async refreshAdminFeeState() {
            const adminFee = this.adminFeeOption();

            if (!adminFee || !this.osobaId) {
                this.adminFeeApplied = false;
                this.recalculateTotal();
                return;
            }

            const params = new URLSearchParams({ udalost: this.eventId });
            if (this.ignorePrihlaskaId) {
                params.set('ignore_prihlaska', this.ignorePrihlaskaId);
            }

            try {
                const response = await fetch(`/ajax/osoba/${this.osobaId}/polozky?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' },
                });

                if (!response.ok) {
                    throw new Error('Nepodařilo se načíst stav administrativního poplatku.');
                }

                const payload = await response.json();
                this.adminFeeApplied = !Boolean(payload.admin_fee_already_charged);
            } catch (error) {
                this.adminFeeApplied = Boolean(this.isEdit && this.adminFeeApplied);
            }

            this.recalculateTotal();
        },
        recalculateTotal() {
            const selectedMoznostiPrice = this.selectedMoznostiItems().reduce((sum, item) => sum + Number(item.cena), 0);
            const selectedUstajeniPrice = this.selectedUstajeniItems().reduce((sum, item) => sum + Number(item.cena), 0);
            const adminFeePrice = this.adminFeeApplied && this.adminFeeOption() ? Number(this.adminFeeOption().cena) : 0;
            this.totalPrice = selectedMoznostiPrice + selectedUstajeniPrice + adminFeePrice;
        },
        selectedItemsCount() {
            return this.selectedMoznosti.length + this.selectedUstajeni.length + ((this.adminFeeApplied && this.adminFeeOption()) ? 1 : 0);
        },
        formatPrice(value) {
            return new Intl.NumberFormat('cs-CZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
        }
    }"
    x-init="init()"
    class="space-y-6"
>
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <form method="POST" action="{{ $isEdit ? route('prihlasky.update', $prihlaska) : route('prihlasky.store', $udalost) }}" class="space-y-6">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <section class="panel px-6 py-5 sm:px-8">
                <div class="flex flex-wrap gap-3">
                    <button type="button" @click="step = 1" :class="step === 1 ? 'bg-[#20392c] text-white border-[#20392c]' : 'bg-white/70 text-gray-600 border-[#ddd0bc]'" class="rounded-full border px-4 py-2 text-sm font-semibold transition">
                        1. Osoba a kůň
                    </button>
                    <button type="button" @click="step = 2" :class="step === 2 ? 'bg-[#20392c] text-white border-[#20392c]' : 'bg-white/70 text-gray-600 border-[#ddd0bc]'" class="rounded-full border px-4 py-2 text-sm font-semibold transition">
                        2. Položky a služby
                    </button>
                    <button type="button" @click="step = 3" :class="step === 3 ? 'bg-[#20392c] text-white border-[#20392c]' : 'bg-white/70 text-gray-600 border-[#ddd0bc]'" class="rounded-full border px-4 py-2 text-sm font-semibold transition">
                        3. Souhrn
                    </button>
                </div>
            </section>

            <section x-cloak x-show="step === 1" class="panel space-y-6 p-6 sm:p-8">
                <div>
                    <p class="section-eyebrow">Krok 1</p>
                    <h2 class="mt-3 text-2xl text-[#20392c]">Vyberte účastníka a koně</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Vyberte účastníka a koně. Tandem kůň je volitelný.</p>
                </div>

                @if($osoby->isEmpty() || $kone->isEmpty())
                    <div class="status-note border-amber-200 bg-amber-50 text-amber-900">
                        Pro vytvoření přihlášky je potřeba mít založenou alespoň jednu osobu a jednoho koně.
                    </div>
                @endif

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <x-input-label for="osoba_id" :value="'Osoba'" />
                        <select id="osoba_id" x-model="osobaId" name="osoba_id" class="field-shell" required>
                            <option value="">Vyberte osobu</option>
                            @foreach($osoby as $osoba)
                                <option value="{{ $osoba->id }}" @selected((int) old('osoba_id', $isEdit ? $prihlaska->osoba_id : 0) === $osoba->id)>
                                    {{ $osoba->prijmeni }} {{ $osoba->jmeno }} ({{ $osoba->datum_narozeni?->format('d.m.Y') }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('osoba_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="kun_id" :value="'Kůň'" />
                        <select id="kun_id" x-model="kunId" name="kun_id" class="field-shell" required>
                            <option value="">Vyberte koně</option>
                            @foreach($kone as $kun)
                                <option value="{{ $kun->id }}" @selected((int) old('kun_id', $isEdit ? $prihlaska->kun_id : 0) === $kun->id)>
                                    {{ $kun->jmeno }} ({{ $kun->plemeno_nazev ?: $kun->plemeno_vlastni ?: $kun->plemeno_kod ?: 'bez plemene' }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('kun_id')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="kun_tandem_id" :value="'Tandem kůň (volitelně)'" />
                    <select id="kun_tandem_id" name="kun_tandem_id" class="field-shell">
                        <option value="">Bez tandemu</option>
                        @foreach($kone as $kun)
                            <option value="{{ $kun->id }}" @selected((int) old('kun_tandem_id', $isEdit ? ($prihlaska->kun_tandem_id ?? 0) : 0) === $kun->id)>
                                {{ $kun->jmeno }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('kun_tandem_id')" class="mt-2" />
                </div>
            </section>

            <section x-cloak x-show="step === 2" class="panel space-y-8 p-6 sm:p-8">
                <div>
                    <p class="section-eyebrow">Krok 2</p>
                    <h2 class="mt-3 text-2xl text-[#20392c]">Zvolte disciplíny a doplňkové služby</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Průběžný součet se přepočítává podle vybraných disciplín a doplňkových služeb.</p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-lg font-semibold text-[#20392c]">Disciplíny</h3>
                        <p class="text-sm text-gray-500">{{ $selectableMoznosti->count() }} možností</p>
                    </div>

                    @if($adminFeeOption)
                        <div class="rounded-[1.25rem] border border-[#eadfcc] bg-[#f9f4eb] px-5 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-[#20392c]">{{ $adminFeeOption->nazev }}</p>
                                    <p class="mt-1 text-sm text-gray-600">Účtuje se automaticky jen jednou za osobu v rámci této akce.</p>
                                </div>
                                <p class="text-sm font-semibold text-[#7b5230]">{{ number_format((float) $adminFeeOption->cena, 2, ',', ' ') }} Kč</p>
                            </div>
                            <p class="mt-3 text-sm text-gray-600" x-show="!osobaId">Vyberte nejprve osobu a poplatek se započte automaticky podle jejích přihlášek.</p>
                            <p class="mt-3 text-sm text-emerald-700" x-cloak x-show="osobaId && adminFeeApplied">Tento poplatek bude připočten k této přihlášce.</p>
                            <p class="mt-3 text-sm text-gray-600" x-cloak x-show="osobaId && !adminFeeApplied">U této osoby už je administrativní poplatek v jiné přihlášce započtený.</p>
                        </div>
                    @endif

                    <div class="space-y-3">
                        @foreach($selectableMoznosti as $moznost)
                            <label class="flex cursor-pointer items-start justify-between gap-4 rounded-[1.25rem] border border-[#eadfcc] bg-white/70 px-5 py-4 transition hover:bg-[#faf6ef]">
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" x-model="selectedMoznosti" name="moznosti[]" value="{{ $moznost->id }}" class="mt-1 rounded border-[#ccb28f] text-[#3d6b4f] focus:ring-[#3d6b4f]"
                                        @checked(in_array($moznost->id, array_map('intval', $selectedMoznosti), true))>
                                    <div>
                                        <p class="font-semibold text-[#20392c]">{{ $moznost->nazev }}</p>
                                        <p class="mt-1 text-sm text-gray-600">
                                            @if($moznost->min_vek !== null)
                                                Minimální věk účastníka: {{ $moznost->min_vek }} let.
                                            @else
                                                Bez minimálního věku.
                                            @endif
                                        </p>
                                        @if($moznost->foto_path || $moznost->pdf_path)
                                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                                @if($moznost->foto_path)
                                                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($moznost->foto_path) }}" target="_blank" rel="noopener" onclick="event.stopPropagation()" class="block overflow-hidden rounded-[1rem] border border-[#eadfcc] bg-white">
                                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($moznost->foto_path) }}" alt="Fotografie disciplíny {{ $moznost->nazev }}" class="h-16 w-16 object-cover">
                                                    </a>
                                                @endif
                                                <div class="flex flex-wrap gap-2">
                                                    @if($moznost->foto_path)
                                                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($moznost->foto_path) }}" target="_blank" rel="noopener" onclick="event.stopPropagation()" class="text-xs font-semibold text-[#7b5230] underline underline-offset-4">Zobrazit obrázek</a>
                                                    @endif
                                                    @if($moznost->pdf_path)
                                                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($moznost->pdf_path) }}" target="_blank" rel="noopener" onclick="event.stopPropagation()" class="text-xs font-semibold text-[#7b5230] underline underline-offset-4">Stáhnout PDF</a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-sm font-semibold text-[#7b5230]">{{ number_format((float) $moznost->cena, 2, ',', ' ') }} Kč</p>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('moznosti')" class="mt-2" />
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-lg font-semibold text-[#20392c]">Ustájení, ubytování a ostatní</h3>
                        <p class="text-sm text-gray-500">{{ $udalost->ustajeniMoznosti->count() }} možností</p>
                    </div>

                    <div class="space-y-3">
                        @foreach($udalost->ustajeniMoznosti as $item)
                            <label class="flex cursor-pointer items-start justify-between gap-4 rounded-[1.25rem] border border-[#eadfcc] bg-white/70 px-5 py-4 transition hover:bg-[#faf6ef]">
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" x-model="selectedUstajeni" name="ustajeni[]" value="{{ $item->id }}" class="mt-1 rounded border-[#ccb28f] text-[#3d6b4f] focus:ring-[#3d6b4f]"
                                        @checked(in_array($item->id, array_map('intval', $selectedUstajeni), true))>
                                    <div>
                                        <p class="font-semibold text-[#20392c]">{{ $item->nazev }}</p>
                                        <p class="mt-1 text-sm text-gray-600">{{ ucfirst($item->typ) }} @if($item->kapacita)• kapacita {{ $item->kapacita }}@endif</p>
                                    </div>
                                </div>
                                <p class="text-sm font-semibold text-[#7b5230]">{{ number_format((float) $item->cena, 2, ',', ' ') }} Kč</p>
                            </label>
                        @endforeach
                    </div>
                </div>
            </section>

            <section x-cloak x-show="step === 3" class="panel space-y-6 p-6 sm:p-8">
                <div>
                    <p class="section-eyebrow">Krok 3</p>
                    <h2 class="mt-3 text-2xl text-[#20392c]">Zkontrolujte souhrn a odešlete přihlášku</h2>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="surface-muted">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#7b5230]">Vybrané disciplíny</p>
                        <ul class="mt-4 space-y-2 text-sm text-gray-700">
                            <template x-if="selectedMoznostiItems().length === 0">
                                <li>Žádná disciplína není vybraná.</li>
                            </template>
                            <template x-for="item in selectedMoznostiItems()" :key="item.id">
                                <li class="flex items-start justify-between gap-4">
                                    <span x-text="item.nazev"></span>
                                    <span class="font-semibold text-[#7b5230]" x-text="`${formatPrice(item.cena)} Kč`"></span>
                                </li>
                            </template>
                            @if($adminFeeOption)
                                <li class="flex items-start justify-between gap-4">
                                    <span>Administrativní poplatek</span>
                                    <span class="font-semibold text-[#7b5230]" x-text="adminFeeApplied ? `${formatPrice(adminFeeOption().cena)} Kč` : 'již započteno jinde'"></span>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div class="surface-muted">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#7b5230]">Doplňkové služby</p>
                        <ul class="mt-4 space-y-2 text-sm text-gray-700">
                            <template x-if="selectedUstajeniItems().length === 0">
                                <li>Bez doplňkových položek.</li>
                            </template>
                            <template x-for="item in selectedUstajeniItems()" :key="`u-${item.id}`">
                                <li class="flex items-start justify-between gap-4">
                                    <span x-text="`${item.nazev} (${item.typ})`"></span>
                                    <span class="font-semibold text-[#7b5230]" x-text="`${formatPrice(item.cena)} Kč`"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <div class="surface-muted">
                    <p class="text-sm text-gray-600">Celkem k úhradě</p>
                    <p class="mt-2 text-3xl font-semibold text-[#20392c]" x-text="`${formatPrice(totalPrice)} Kč`"></p>
                </div>

                <div>
                    <x-input-label for="poznamka" :value="'Poznámka pro pořadatele'" />
                    <textarea id="poznamka" name="poznamka" rows="4" class="field-shell">{{ old('poznamka', $isEdit ? $prihlaska->poznamka : '') }}</textarea>
                    <x-input-error :messages="$errors->get('poznamka')" class="mt-2" />
                </div>

                <label for="gdpr_souhlas" class="flex items-start gap-3 rounded-[1rem] border border-[#eadfcc] bg-white/60 px-4 py-4 text-sm leading-6 text-gray-700">
                    <input id="gdpr_souhlas" type="checkbox" name="gdpr_souhlas" value="1" class="mt-1 rounded border-[#ccb28f] text-[#3d6b4f] focus:ring-[#3d6b4f]" @checked(old('gdpr_souhlas', true)) required>
                    <span>Potvrzuji souhlas se zpracováním osobních údajů pro vytvoření a správu této přihlášky.</span>
                </label>
                <x-input-error :messages="$errors->get('gdpr_souhlas')" class="mt-2" />
            </section>

            <section class="panel flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <button type="button" @click="prevStep()" class="button-secondary" x-show="step > 1">Zpět</button>
                    <button type="button" @click="nextStep()" class="button-primary" x-show="step < 3">Pokračovat</button>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ $isEdit ? route('prihlasky.show', $prihlaska) : route('udalosti.show', $udalost) }}" class="text-sm text-[#7b5230] underline underline-offset-4">
                        {{ $isEdit ? 'Zpět na detail přihlášky' : 'Zpět na detail akce' }}
                    </a>
                    <x-primary-button x-show="step === 3">
                        {{ $isEdit ? 'Uložit změny' : 'Odeslat přihlášku' }}
                    </x-primary-button>
                </div>
            </section>
        </form>

        <aside class="space-y-6">
            <section class="panel p-6">
                <p class="section-eyebrow">Souhrn</p>
                <h3 class="mt-3 text-2xl text-[#20392c]">Průběžná cena</h3>
                <p class="mt-4 text-4xl font-semibold text-[#20392c]" x-text="`${formatPrice(totalPrice)} Kč`"></p>
                <p class="mt-3 text-sm leading-6 text-gray-600">Vybraných položek: <span class="font-semibold text-[#20392c]" x-text="selectedItemsCount()"></span></p>
            </section>

            <section class="panel p-6">
                <p class="section-eyebrow">Kontrola</p>
                <ul class="mt-4 space-y-3 text-sm text-gray-700">
                    <li class="flex items-start justify-between gap-4">
                        <span>Osoba</span>
                        <span :class="osobaId ? 'text-emerald-700' : 'text-amber-700'" x-text="osobaId ? 'vybrána' : 'chybí'"></span>
                    </li>
                    <li class="flex items-start justify-between gap-4">
                        <span>Kůň</span>
                        <span :class="kunId ? 'text-emerald-700' : 'text-amber-700'" x-text="kunId ? 'vybrán' : 'chybí'"></span>
                    </li>
                    <li class="flex items-start justify-between gap-4">
                        <span>Disciplíny</span>
                        <span :class="selectedMoznosti.length ? 'text-emerald-700' : 'text-amber-700'" x-text="selectedMoznosti.length ? `${selectedMoznosti.length} vybráno` : 'nic nevybráno'"></span>
                    </li>
                </ul>
            </section>
        </aside>
    </div>
</div>
