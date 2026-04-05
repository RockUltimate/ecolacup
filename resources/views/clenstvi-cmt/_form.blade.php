@php
    $isEdit = isset($clenstvi);
    $types = (array) config('clenstvi_cmt.membership_types', []);
    $yearlyPrices = (array) config('clenstvi_cmt.yearly_prices', []);
    $defaultPrices = collect($types)
        ->mapWithKeys(fn (array $item, string $key) => [$key => (float) ($item['default_price'] ?? 0)])
        ->all();
    $firstType = array_key_first($types) ?? 'fyzicka_osoba';
    $selectedType = (string) old('typ_clenstvi', $clenstvi->typ_clenstvi ?? $firstType);
    if (! array_key_exists($selectedType, $types)) {
        $selectedType = $firstType;
    }
    $selectedYear = (int) old('rok', $clenstvi->rok ?? now()->year);
    $selectedPrice = (float) old(
        'cena',
        $clenstvi->cena ?? ($yearlyPrices[$selectedYear][$selectedType] ?? $defaultPrices[$selectedType] ?? 0)
    );
@endphp

<form
    method="POST"
    action="{{ $isEdit ? route('clenstvi-cmt.update', $clenstvi) : route('clenstvi-cmt.store') }}"
    class="space-y-6"
    enctype="multipart/form-data"
    x-data="{
        typClenstvi: @js($selectedType),
        rok: @js($selectedYear),
        cena: @js($selectedPrice),
        selectedOsobaId: '{{ old('osoba_id', $clenstvi->osoba_id ?? '') }}',
        isEdit: @js($isEdit),
        loadingOsoba: false,
        isNewMember: false,
        newMemberAdminFee: Number(@js(config('clenstvi_cmt.new_member_admin_fee', 100))),
        yearlyPrices: @js($yearlyPrices),
        defaultPrices: @js($defaultPrices),
        resolvePrice(type, year) {
            const key = String(year ?? '');
            const byYear = this.yearlyPrices[key] ?? this.yearlyPrices[Number(key)] ?? null;
            if (byYear && byYear[type] !== undefined) {
                return Number(byYear[type]);
            }

            if (this.defaultPrices[type] !== undefined) {
                return Number(this.defaultPrices[type]);
            }

            return Number(this.cena || 0);
        },
        setType(type) {
            this.typClenstvi = type;
            this.cena = this.resolvePrice(type, this.rok);
        },
        setYear(yearValue) {
            const parsed = Number(yearValue);
            if (!Number.isFinite(parsed)) {
                return;
            }

            this.rok = parsed;
            this.cena = this.resolvePrice(this.typClenstvi, parsed);
        },
        formatPrice(value) {
            return new Intl.NumberFormat('cs-CZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(value || 0));
        },
        displayPriceWithFee() {
            const basePrice = Number(this.cena || 0);
            if (this.isEdit || !this.isNewMember) {
                return basePrice;
            }

            return basePrice + Number(this.newMemberAdminFee || 0);
        },
        async loadOsobaData() {
            if (!this.selectedOsobaId) {
                this.isNewMember = false;
                return;
            }

            this.loadingOsoba = true;
            try {
                const response = await fetch(`/ajax/osoba/${this.selectedOsobaId}/clenstvi-data`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                const kontakt = payload.kontakt || {};
                const telefonInput = document.getElementById('telefon');
                const emailInput = document.getElementById('email');
                const bydlisteInput = document.getElementById('bydliste');

                if (telefonInput && (telefonInput.value === '' || !this.isEdit)) {
                    telefonInput.value = kontakt.telefon || '';
                }
                if (emailInput && (emailInput.value === '' || !this.isEdit)) {
                    emailInput.value = kontakt.email || '';
                }
                if (bydlisteInput && (bydlisteInput.value === '' || !this.isEdit)) {
                    bydlisteInput.value = kontakt.bydliste || '';
                }

                this.isNewMember = Boolean(payload.is_new_member);
                if (payload.new_member_admin_fee !== undefined && payload.new_member_admin_fee !== null) {
                    this.newMemberAdminFee = Number(payload.new_member_admin_fee);
                }

                if (!this.isEdit && payload.posledni_clenstvi && payload.posledni_clenstvi.typ_clenstvi) {
                    this.typClenstvi = payload.posledni_clenstvi.typ_clenstvi;
                }

                if (!this.isEdit) {
                    this.cena = this.resolvePrice(this.typClenstvi, this.rok);
                }
            } catch (e) {
                // silent fallback when autocomplete endpoint is unavailable
            } finally {
                this.loadingOsoba = false;
            }
        },
    }"
    x-init="if (selectedOsobaId) { loadOsobaData(); }"
>
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="panel p-5 space-y-4">
        <h3 class="text-base font-semibold text-on-surface dark:text-[#e5e2dd]">Člen a typ členství</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="osoba_id" :value="'Osoba'" />
                <select
                    id="osoba_id"
                    name="osoba_id"
                    class="mt-1 block w-full border-outline-variant dark:border-[#43493e] rounded-md shadow-sm"
                    x-model="selectedOsobaId"
                    @change="loadOsobaData"
                    required
                >
                    <option value="">Vyberte osobu</option>
                    @foreach($osoby as $osoba)
                        <option value="{{ $osoba->id }}" @selected((int) old('osoba_id', $clenstvi->osoba_id ?? 0) === (int) $osoba->id)>
                            {{ $osoba->prijmeni }} {{ $osoba->jmeno }}
                        </option>
                    @endforeach
                </select>
                <p x-show="loadingOsoba" class="mt-2 text-xs text-on-surface-variant dark:text-[#c3c8bb]">Načítám údaje osoby…</p>
                <x-input-error :messages="$errors->get('osoba_id')" class="mt-2" />
            </div>
            <div>
                <x-input-label :value="'Typ členství'" />
                <input type="hidden" id="typ_clenstvi" name="typ_clenstvi" x-model="typClenstvi">
                <div class="mt-2 grid grid-cols-1 gap-2">
                    @foreach($types as $key => $meta)
                        <button
                            type="button"
                            @click="setType('{{ $key }}')"
                            class="text-left rounded-lg border px-3 py-2 transition"
                            :class="typClenstvi === '{{ $key }}' ? 'border-primary bg-primary-fixed/20' : 'border-outline-variant dark:border-[#43493e] bg-surface-container-lowest dark:bg-[#252522]'"
                        >
                            <span class="block text-sm font-semibold text-on-surface dark:text-[#e5e2dd]">{{ $meta['label'] }}</span>
                            <span class="block text-xs text-on-surface-variant dark:text-[#c3c8bb]" x-text="`${formatPrice(resolvePrice('{{ $key }}', rok))} Kč / rok`"></span>
                        </button>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('typ_clenstvi')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="panel p-5 space-y-4">
        <h3 class="text-base font-semibold text-on-surface dark:text-[#e5e2dd]">Platební a kontaktní údaje</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-input-label for="rok" :value="'Rok'" />
                <x-text-input id="rok" name="rok" type="number" class="mt-1 block w-full" x-model.number="rok" @change="setYear($event.target.value)" required />
                <x-input-error :messages="$errors->get('rok')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="cena" :value="'Cena (Kč)'" />
                <x-text-input id="cena" name="cena" type="number" step="0.01" class="mt-1 block w-full" x-model.number="cena" required readonly />
                <p x-show="!isEdit && isNewMember" class="mt-2 text-xs text-on-surface-variant dark:text-[#c3c8bb]">
                    Jednorázový administrativní poplatek nového člena:
                    <span class="font-semibold" x-text="`${formatPrice(newMemberAdminFee)} Kč`"></span>.
                    Celkem při vytvoření:
                    <span class="font-semibold text-[#3d6b4f]" x-text="`${formatPrice(displayPriceWithFee())} Kč`"></span>.
                </p>
                <x-input-error :messages="$errors->get('cena')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="evidencni_cislo" :value="'Evidenční číslo'" />
                <x-text-input id="evidencni_cislo" name="evidencni_cislo" type="text" class="mt-1 block w-full" :value="old('evidencni_cislo', $clenstvi->evidencni_cislo ?? '')" />
                <x-input-error :messages="$errors->get('evidencni_cislo')" class="mt-2" />
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="telefon" :value="'Telefon'" />
                <x-text-input id="telefon" name="telefon" type="text" class="mt-1 block w-full" :value="old('telefon', $clenstvi->telefon ?? '')" />
                <x-input-error :messages="$errors->get('telefon')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="email" :value="'E-mail'" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $clenstvi->email ?? '')" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        </div>
        <div>
            <x-input-label for="bydliste" :value="'Bydliště'" />
            <x-text-input id="bydliste" name="bydliste" type="text" class="mt-1 block w-full" :value="old('bydliste', $clenstvi->bydliste ?? '')" />
            <x-input-error :messages="$errors->get('bydliste')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="sken_prihlaska_upload" :value="'Nahrát sken přihlášky (jpg/png/webp/pdf)'" />
            <input
                id="sken_prihlaska_upload"
                name="sken_prihlaska_upload"
                type="file"
                accept=".jpg,.jpeg,.png,.webp,.pdf"
                class="mt-1 block w-full text-sm text-on-surface dark:text-[#e5e2dd] file:mr-3 file:rounded-md file:border-0 file:bg-[#3d6b4f] file:px-3 file:py-2 file:text-white hover:file:bg-[#31563f]"
            >
            <x-input-error :messages="$errors->get('sken_prihlaska_upload')" class="mt-2" />
            @if(isset($clenstvi) && $clenstvi->sken_prihlaska)
                <p class="mt-2 text-sm text-on-surface-variant dark:text-[#c3c8bb]">
                    Aktuální soubor:
                    <a href="{{ asset('storage/'.$clenstvi->sken_prihlaska) }}" target="_blank" rel="noopener" class="brand-link">
                        zobrazit sken
                    </a>
                </p>
            @endif
        </div>
    </div>
    <div class="panel p-5 space-y-2">
        <h3 class="text-base font-semibold text-on-surface dark:text-[#e5e2dd]">Souhlasy a aktivace</h3>
        <label for="aktivni" class="inline-flex items-center">
            <input id="aktivni" type="checkbox" name="aktivni" value="1" class="rounded border-outline-variant dark:border-[#43493e] text-primary shadow-sm focus:ring-indigo-500" @checked(old('aktivni', $clenstvi->aktivni ?? true))>
            <span class="ms-2 text-sm text-on-surface dark:text-[#e5e2dd]">Aktivní členství</span>
        </label>
        <label for="souhlas_gdpr" class="inline-flex items-center">
            <input id="souhlas_gdpr" type="checkbox" name="souhlas_gdpr" value="1" class="rounded border-outline-variant dark:border-[#43493e] text-primary shadow-sm focus:ring-indigo-500" @checked(old('souhlas_gdpr', $clenstvi->souhlas_gdpr ?? false))>
            <span class="ms-2 text-sm text-on-surface dark:text-[#e5e2dd]">Souhlas GDPR</span>
        </label>
        <label for="souhlas_email" class="inline-flex items-center">
            <input id="souhlas_email" type="checkbox" name="souhlas_email" value="1" class="rounded border-outline-variant dark:border-[#43493e] text-primary shadow-sm focus:ring-indigo-500" @checked(old('souhlas_email', $clenstvi->souhlas_email ?? false))>
            <span class="ms-2 text-sm text-on-surface dark:text-[#e5e2dd]">Souhlas se zasíláním e-mailů</span>
        </label>
        <label for="souhlas_zverejneni" class="inline-flex items-center">
            <input id="souhlas_zverejneni" type="checkbox" name="souhlas_zverejneni" value="1" class="rounded border-outline-variant dark:border-[#43493e] text-primary shadow-sm focus:ring-indigo-500" @checked(old('souhlas_zverejneni', $clenstvi->souhlas_zverejneni ?? false))>
            <span class="ms-2 text-sm text-on-surface dark:text-[#e5e2dd]">Souhlas se zveřejněním</span>
        </label>
    </div>

    <div class="flex items-center gap-3">
        <x-primary-button>{{ $isEdit ? 'Uložit členství' : 'Vytvořit členství' }}</x-primary-button>
        <a href="{{ route('clenstvi-cmt.index') }}" class="text-sm text-on-surface-variant dark:text-[#c3c8bb] hover:text-on-surface dark:hover:text-[#e5e2dd] underline">Zpět</a>
    </div>
</form>
