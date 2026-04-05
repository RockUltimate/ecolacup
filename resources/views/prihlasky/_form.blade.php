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
        adminFeeStatus: { alreadyCharged: false, hasMembership: true, autoFeeRequired: false, loading: false },
        moznostiMeta: @js($udalost->moznosti->map(fn ($m) => ['id' => (int) $m->id, 'nazev' => $m->nazev, 'cena' => (float) $m->cena, 'je_administrativni_poplatek' => (bool) $m->je_administrativni_poplatek])->values()),
        ustajeniMeta: @js($udalost->ustajeniMoznosti->map(fn ($u) => ['id' => (int) $u->id, 'nazev' => $u->nazev, 'typ' => $u->typ, 'cena' => (float) $u->cena])->values()),
        totalPrice: 0,
        init() {
            this.recalculateTotal();
            if (this.osobaId) {
                this.fetchClenstviStatus();
                this.fetchAdminFeeStatus();
            }
            if (this.kunId) {
                this.fetchOckovaniStatus();
            }
            this.$watch('selectedMoznosti', () => this.recalculateTotal());
            this.$watch('selectedUstajeni', () => this.recalculateTotal());
            this.$watch('osobaId', () => {
                this.fetchClenstviStatus();
                this.fetchAdminFeeStatus();
            });
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
        adminFeeOption() {
            return this.moznostiMeta.find(item => Boolean(item.je_administrativni_poplatek)) ?? null;
        },
        hasSelectedAdminFee() {
            const adminFee = this.adminFeeOption();
            return adminFee ? this.selectedMoznosti.map(Number).includes(Number(adminFee.id)) : false;
        },
        recalculateTotal() {
            const selectedMoznostiPrice = this.selectedMoznostiItems().reduce((sum, item) => sum + Number(item.cena), 0);
            const selectedUstajeniPrice = this.selectedUstajeniItems().reduce((sum, item) => sum + Number(item.cena), 0);
            const adminFee = this.adminFeeOption();
            const adminFeePrice = adminFee ? Number(adminFee.cena) : 0;
            const hasSelectedAdminFee = this.hasSelectedAdminFee();

            let correctedMoznostiPrice = selectedMoznostiPrice;
            if (this.adminFeeStatus.alreadyCharged && hasSelectedAdminFee) {
                correctedMoznostiPrice -= adminFeePrice;
            } else if (this.adminFeeStatus.autoFeeRequired && !hasSelectedAdminFee) {
                correctedMoznostiPrice += adminFeePrice;
            }

            this.totalPrice = correctedMoznostiPrice + selectedUstajeniPrice;
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
        },
        async fetchAdminFeeStatus() {
            if (!this.osobaId) {
                this.adminFeeStatus = { alreadyCharged: false, hasMembership: true, autoFeeRequired: false, loading: false };
                this.recalculateTotal();
                return;
            }

            this.adminFeeStatus.loading = true;
            try {
                const response = await fetch(`/ajax/udalost/{{ $udalost->id }}/admin-poplatek?osoba=${this.osobaId}`);
                if (!response.ok) return;
                const payload = await response.json();
                this.adminFeeStatus = {
                    alreadyCharged: Boolean(payload.already_charged),
                    hasMembership: Boolean(payload.has_membership),
                    autoFeeRequired: Boolean(payload.auto_fee_required),
                    loading: false,
                };
                this.recalculateTotal();
            } catch (e) {
                this.adminFeeStatus.loading = false;
            }
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

            <section class="rounded-2xl bg-surface-container-low px-6 py-5 dark:bg-[#252522] sm:px-8">
                <div class="flex flex-wrap gap-3">
                    <button type="button" @click="step = 1" :class="step === 1 ? 'bg-[#20392c] text-white border-[#20392c]' : 'bg-surface-container-lowest/80 dark:bg-[#2a2a27]/80 text-on-surface-variant dark:text-[#c3c8bb] border-outline-variant/40 dark:border-[#43493e]/40'" class="rounded-full border px-4 py-2 text-sm font-semibold transition">
                        1. Osoba a kůň
                    </button>
                    <button type="button" @click="step = 2" :class="step === 2 ? 'bg-[#20392c] text-white border-[#20392c]' : 'bg-surface-container-lowest/80 dark:bg-[#2a2a27]/80 text-on-surface-variant dark:text-[#c3c8bb] border-outline-variant/40 dark:border-[#43493e]/40'" class="rounded-full border px-4 py-2 text-sm font-semibold transition">
                        2. Položky a služby
                    </button>
                    <button type="button" @click="step = 3" :class="step === 3 ? 'bg-[#20392c] text-white border-[#20392c]' : 'bg-surface-container-lowest/80 dark:bg-[#2a2a27]/80 text-on-surface-variant dark:text-[#c3c8bb] border-outline-variant/40 dark:border-[#43493e]/40'" class="rounded-full border px-4 py-2 text-sm font-semibold transition">
                        3. Souhrn
                    </button>
                </div>
            </section>

            <section x-cloak x-show="step === 1" class="rounded-2xl bg-surface-container-low space-y-6 p-6 dark:bg-[#252522] sm:p-8">
                <div>
                    <p class="section-eyebrow">Krok 1</p>
                    <h2 class="mt-3 text-2xl text-on-surface dark:text-[#e5e2dd]">Vyberte účastníka a koně</h2>
                    <p class="mt-2 text-sm leading-6 text-on-surface-variant dark:text-[#c3c8bb]">Při úpravě zůstává osoba i hlavní kůň uzamčený, aby zůstala zachovaná historie registrace.</p>
                </div>

                @if($osoby->isEmpty() || $kone->isEmpty())
                    <div class="status-note mt-4">
                        Pro vytvoření přihlášky je potřeba mít založenou alespoň jednu osobu a jednoho koně.
                    </div>
                @endif

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="osoba_id" class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Osoba</label>
                        <select id="osoba_id" x-model="osobaId" name="osoba_id" class="field-shell" {{ $isEdit ? 'disabled' : '' }} required>
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
                        <label for="kun_id" class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Kůň</label>
                        <select id="kun_id" x-model="kunId" name="kun_id" class="field-shell" {{ $isEdit ? 'disabled' : '' }} required>
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
                    <label for="kun_tandem_id" class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Tandem kůň (volitelně)</label>
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

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="surface-muted">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-secondary dark:text-secondary-fixed-dim">Členství CMT</p>
                        <p class="mt-3 text-lg font-semibold text-on-surface dark:text-[#e5e2dd]" x-text="clenstvi.label"></p>
                    </div>
                    <div class="surface-muted">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-secondary dark:text-secondary-fixed-dim">Očkování a vyšetření</p>
                        <ul class="mt-3 space-y-2 text-sm text-on-surface dark:text-[#e5e2dd]">
                            <li>EHV: <span x-text="ockovani.ehv_datum ?? 'neuvedeno'"></span></li>
                            <li>AIE: <span x-text="ockovani.aie_datum ?? 'neuvedeno'"></span></li>
                            <li>Chřipka: <span x-text="ockovani.chripka_datum ?? 'neuvedeno'"></span></li>
                        </ul>
                    </div>
                </div>
            </section>

            <section x-cloak x-show="step === 2" class="rounded-2xl bg-surface-container-low space-y-8 p-6 dark:bg-[#252522] sm:p-8">
                <div>
                    <p class="section-eyebrow">Krok 2</p>
                    <h2 class="mt-3 text-2xl text-on-surface dark:text-[#e5e2dd]">Zvolte disciplíny a doplňkové služby</h2>
                    <p class="mt-2 text-sm leading-6 text-on-surface-variant dark:text-[#c3c8bb]">Průběžný součet dole počítá i s administrativním poplatkem podle pravidel členství CMT.</p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-lg font-semibold text-on-surface dark:text-[#e5e2dd]">Disciplíny</h3>
                        <p class="text-sm text-gray-500">{{ $udalost->moznosti->count() }} možností</p>
                    </div>

                    <div class="space-y-3">
                        @foreach($udalost->moznosti as $moznost)
                            <label class="flex cursor-pointer items-start justify-between gap-4 rounded-[1.25rem] border border-outline-variant/30 dark:border-[#43493e]/30 bg-surface-container-lowest/80 dark:bg-[#2a2a27]/80 px-5 py-4 transition hover:bg-[#faf6ef]">
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" x-model="selectedMoznosti" name="moznosti[]" value="{{ $moznost->id }}" class="mt-1 rounded border-[#ccb28f] text-[#3d6b4f] focus:ring-[#3d6b4f]"
                                        @checked(in_array($moznost->id, array_map('intval', $selectedMoznosti), true))>
                                    <div>
                                        <p class="font-semibold text-on-surface dark:text-[#e5e2dd]">{{ $moznost->nazev }}</p>
                                        <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">
                                            @if($moznost->je_administrativni_poplatek)
                                                Administrativní položka dle pravidel akce.
                                            @elseif($moznost->min_vek !== null)
                                                Minimální věk účastníka: {{ $moznost->min_vek }} let.
                                            @else
                                                Bez minimálního věku.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <p class="text-sm font-semibold text-secondary dark:text-secondary-fixed-dim">{{ number_format((float) $moznost->cena, 2, ',', ' ') }} Kč</p>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('moznosti')" class="mt-2" />

                    <p x-show="adminFeeStatus.loading" class="text-sm text-gray-500">Kontroluji administrativní poplatek…</p>
                    <div x-show="!adminFeeStatus.loading && adminFeeStatus.autoFeeRequired" class="status-note mt-4">
                        U osoby bez členství CMT bude při první přihlášce na tuto akci automaticky započten administrativní poplatek.
                    </div>
                    <div x-show="!adminFeeStatus.loading && adminFeeStatus.alreadyCharged" class="status-note mt-4">
                        Administrativní poplatek už byl pro tuto osobu v rámci akce účtován a nebude přidán znovu.
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-lg font-semibold text-on-surface dark:text-[#e5e2dd]">Ustájení, ubytování a ostatní</h3>
                        <p class="text-sm text-gray-500">{{ $udalost->ustajeniMoznosti->count() }} možností</p>
                    </div>

                    <div class="space-y-3">
                        @foreach($udalost->ustajeniMoznosti as $item)
                            <label class="flex cursor-pointer items-start justify-between gap-4 rounded-[1.25rem] border border-outline-variant/30 dark:border-[#43493e]/30 bg-surface-container-lowest/80 dark:bg-[#2a2a27]/80 px-5 py-4 transition hover:bg-[#faf6ef]">
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" x-model="selectedUstajeni" name="ustajeni[]" value="{{ $item->id }}" class="mt-1 rounded border-[#ccb28f] text-[#3d6b4f] focus:ring-[#3d6b4f]"
                                        @checked(in_array($item->id, array_map('intval', $selectedUstajeni), true))>
                                    <div>
                                        <p class="font-semibold text-on-surface dark:text-[#e5e2dd]">{{ $item->nazev }}</p>
                                        <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">{{ ucfirst($item->typ) }} @if($item->kapacita)• kapacita {{ $item->kapacita }}@endif</p>
                                    </div>
                                </div>
                                <p class="text-sm font-semibold text-secondary dark:text-secondary-fixed-dim">{{ number_format((float) $item->cena, 2, ',', ' ') }} Kč</p>
                            </label>
                        @endforeach
                    </div>
                </div>
            </section>

            <section x-cloak x-show="step === 3" class="rounded-2xl bg-surface-container-low space-y-6 p-6 dark:bg-[#252522] sm:p-8">
                <div>
                    <p class="section-eyebrow">Krok 3</p>
                    <h2 class="mt-3 text-2xl text-on-surface dark:text-[#e5e2dd]">Zkontrolujte souhrn a odešlete přihlášku</h2>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="surface-muted">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-secondary dark:text-secondary-fixed-dim">Vybrané disciplíny</p>
                        <ul class="mt-4 space-y-2 text-sm text-on-surface dark:text-[#e5e2dd]">
                            <template x-if="selectedMoznostiItems().length === 0">
                                <li>Žádná disciplína není vybraná.</li>
                            </template>
                            <template x-for="item in selectedMoznostiItems()" :key="item.id">
                                <li class="flex items-start justify-between gap-4">
                                    <span x-text="item.nazev"></span>
                                    <span class="font-semibold text-secondary dark:text-secondary-fixed-dim" x-text="`${formatPrice(item.cena)} Kč`"></span>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <div class="surface-muted">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-secondary dark:text-secondary-fixed-dim">Doplňkové služby</p>
                        <ul class="mt-4 space-y-2 text-sm text-on-surface dark:text-[#e5e2dd]">
                            <template x-if="selectedUstajeniItems().length === 0">
                                <li>Bez doplňkových položek.</li>
                            </template>
                            <template x-for="item in selectedUstajeniItems()" :key="`u-${item.id}`">
                                <li class="flex items-start justify-between gap-4">
                                    <span x-text="`${item.nazev} (${item.typ})`"></span>
                                    <span class="font-semibold text-secondary dark:text-secondary-fixed-dim" x-text="`${formatPrice(item.cena)} Kč`"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <div class="surface-muted">
                    <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">Celkem k úhradě</p>
                    <p class="mt-2 text-3xl font-semibold text-on-surface dark:text-[#e5e2dd]" x-text="`${formatPrice(totalPrice)} Kč`"></p>
                </div>

                <div>
                    <label for="poznamka" class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Poznámka pro pořadatele</label>
                    <textarea id="poznamka" name="poznamka" rows="4" class="field-shell">{{ old('poznamka', $isEdit ? $prihlaska->poznamka : '') }}</textarea>
                    <x-input-error :messages="$errors->get('poznamka')" class="mt-2" />
                </div>

                <label for="gdpr_souhlas" class="flex items-start gap-3 rounded-[1rem] border border-outline-variant/30 dark:border-[#43493e]/30 bg-surface-container-lowest/60 dark:bg-[#2a2a27]/60 px-4 py-4 text-sm leading-6 text-on-surface dark:text-[#e5e2dd]">
                    <input id="gdpr_souhlas" type="checkbox" name="gdpr_souhlas" value="1" class="mt-1 rounded border-[#ccb28f] text-[#3d6b4f] focus:ring-[#3d6b4f]" @checked(old('gdpr_souhlas', true)) required>
                    <span>Potvrzuji souhlas se zpracováním osobních údajů pro vytvoření a správu této přihlášky.</span>
                </label>
                <x-input-error :messages="$errors->get('gdpr_souhlas')" class="mt-2" />
            </section>

            <section class="rounded-2xl bg-surface-container-low flex flex-col gap-4 p-5 dark:bg-[#252522] sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <button type="button" @click="prevStep()" class="button-secondary" x-show="step > 1">Zpět</button>
                    <button type="button" @click="nextStep()" class="button-primary" x-show="step < 3">Pokračovat</button>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ $isEdit ? route('prihlasky.show', $prihlaska) : route('udalosti.show', $udalost) }}" class="text-sm text-secondary dark:text-secondary-fixed-dim underline underline-offset-4">
                        {{ $isEdit ? 'Zpět na detail přihlášky' : 'Zpět na detail akce' }}
                    </a>
                    <button type="submit" class="button-primary" x-show="step === 3">
                        {{ $isEdit ? 'Uložit změny' : 'Odeslat přihlášku' }}
                    </button>
                </div>
            </section>
        </form>

        <aside class="space-y-6">
            <section class="glass-card sticky top-24 p-6">
                <p class="section-eyebrow">Souhrn</p>
                <h3 class="mt-3 text-2xl text-on-surface dark:text-[#e5e2dd]">Průběžná cena</h3>
                <p class="mt-4 text-4xl font-semibold text-on-surface dark:text-[#e5e2dd]" x-text="`${formatPrice(totalPrice)} Kč`"></p>
                <p class="mt-3 text-sm leading-6 text-on-surface-variant dark:text-[#c3c8bb]">Vybraných položek: <span class="font-semibold text-on-surface dark:text-[#e5e2dd]" x-text="selectedMoznosti.length + selectedUstajeni.length"></span></p>
            </section>

            <section class="glass-card p-6">
                <p class="section-eyebrow">Kontrola</p>
                <ul class="mt-4 space-y-3 text-sm text-on-surface dark:text-[#e5e2dd]">
                    <li class="flex items-start justify-between gap-4">
                        <span>Osoba</span>
                        <span :class="osobaId ? 'text-primary dark:text-inverse-primary' : 'text-tertiary dark:text-tertiary-fixed-dim'" x-text="osobaId ? 'vybrána' : 'chybí'"></span>
                    </li>
                    <li class="flex items-start justify-between gap-4">
                        <span>Kůň</span>
                        <span :class="kunId ? 'text-primary dark:text-inverse-primary' : 'text-tertiary dark:text-tertiary-fixed-dim'" x-text="kunId ? 'vybrán' : 'chybí'"></span>
                    </li>
                    <li class="flex items-start justify-between gap-4">
                        <span>Disciplíny</span>
                        <span :class="selectedMoznosti.length ? 'text-primary dark:text-inverse-primary' : 'text-tertiary dark:text-tertiary-fixed-dim'" x-text="selectedMoznosti.length ? `${selectedMoznosti.length} vybráno` : 'nic nevybráno'"></span>
                    </li>
                </ul>
            </section>
        </aside>
    </div>
</div>
