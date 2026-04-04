@php
    $isEdit = isset($kun);
@endphp

<form method="POST" action="{{ $isEdit ? route('kone.update', $kun) : route('kone.store') }}" class="space-y-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif
    <div x-data="{ customBreed: @js((bool) old('plemeno_vlastni', $kun->plemeno_vlastni ?? null)) }" class="space-y-6">
        <section class="panel p-5 space-y-4">
            <h3 class="text-base font-semibold text-gray-900">Základní údaje</h3>
            <div>
                <x-input-label for="jmeno" :value="'Jméno koně'" />
                <x-text-input id="jmeno" name="jmeno" type="text" class="mt-1 block w-full" :value="old('jmeno', $kun->jmeno ?? '')" required />
                <x-input-error :messages="$errors->get('jmeno')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="plemeno_kod" :value="'Plemeno (kód)'" />
                <select id="plemeno_kod" name="plemeno_kod" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">Vyberte plemeno</option>
                    @foreach($plemena as $pleme)
                        <option value="{{ $pleme->kod }}" @selected(old('plemeno_kod', $kun->plemeno_kod ?? '') === $pleme->kod)>
                            {{ $pleme->kod }} — {{ $pleme->nazev }}
                        </option>
                    @endforeach
                </select>
                <button type="button" class="mt-2 text-sm text-indigo-600 hover:text-indigo-800 underline" @click="customBreed = !customBreed">
                    Nenašli jste plemeno? Zadat ručně
                </button>
                <x-input-error :messages="$errors->get('plemeno_kod')" class="mt-2" />
            </div>

            <div x-show="customBreed">
                <x-input-label for="plemeno_vlastni" :value="'Vlastní název plemene (volitelné)'" />
                <x-text-input id="plemeno_vlastni" name="plemeno_vlastni" type="text" class="mt-1 block w-full" :value="old('plemeno_vlastni', $kun->plemeno_vlastni ?? '')" />
                <x-input-error :messages="$errors->get('plemeno_vlastni')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="rok_narozeni" :value="'Rok narození'" />
                    <x-text-input id="rok_narozeni" name="rok_narozeni" type="number" min="1900" max="{{ now()->year }}" class="mt-1 block w-full" :value="old('rok_narozeni', $kun->rok_narozeni ?? '')" required />
                    <x-input-error :messages="$errors->get('rok_narozeni')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="pohlavi" :value="'Pohlaví'" />
                    <select id="pohlavi" name="pohlavi" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="">Vyberte</option>
                        <option value="h" @selected(old('pohlavi', $kun->pohlavi ?? '') === 'h')>Hřebec</option>
                        <option value="k" @selected(old('pohlavi', $kun->pohlavi ?? '') === 'k')>Klisna</option>
                        <option value="v" @selected(old('pohlavi', $kun->pohlavi ?? '') === 'v')>Valach</option>
                    </select>
                    <x-input-error :messages="$errors->get('pohlavi')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="staj" :value="'Stáj'" />
                <x-text-input id="staj" name="staj" type="text" class="mt-1 block w-full" :value="old('staj', $kun->staj ?? '')" required />
                <x-input-error :messages="$errors->get('staj')" class="mt-2" />
            </div>
        </section>

        <section class="panel p-5 space-y-4">
            <h3 class="text-base font-semibold text-gray-900">Zdravotní údaje</h3>
            <p class="text-xs text-gray-600">Uveďte platné termíny vyšetření/očkování. Chybějící data se označí jako nekompletní.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="ehv_datum" :value="'EHV datum'" />
                    <x-text-input id="ehv_datum" name="ehv_datum" type="date" class="mt-1 block w-full" :value="old('ehv_datum', isset($kun) && $kun->ehv_datum ? $kun->ehv_datum->format('Y-m-d') : '')" />
                    <x-input-error :messages="$errors->get('ehv_datum')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="aie_datum" :value="'AIE datum'" />
                    <x-text-input id="aie_datum" name="aie_datum" type="date" class="mt-1 block w-full" :value="old('aie_datum', isset($kun) && $kun->aie_datum ? $kun->aie_datum->format('Y-m-d') : '')" />
                    <x-input-error :messages="$errors->get('aie_datum')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="chripka_datum" :value="'Chřipka datum'" />
                    <x-text-input id="chripka_datum" name="chripka_datum" type="date" class="mt-1 block w-full" :value="old('chripka_datum', isset($kun) && $kun->chripka_datum ? $kun->chripka_datum->format('Y-m-d') : '')" />
                    <x-input-error :messages="$errors->get('chripka_datum')" class="mt-2" />
                </div>
            </div>
        </section>

        <section class="panel p-5 space-y-4">
            <h3 class="text-base font-semibold text-gray-900">Průkaz a vlastník</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="cislo_prukazu" :value="'Číslo průkazu'" />
                    <x-text-input id="cislo_prukazu" name="cislo_prukazu" type="text" class="mt-1 block w-full" :value="old('cislo_prukazu', $kun->cislo_prukazu ?? '')" />
                    <x-input-error :messages="$errors->get('cislo_prukazu')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="cislo_hospodarstvi" :value="'Číslo hospodářství'" />
                    <x-text-input id="cislo_hospodarstvi" name="cislo_hospodarstvi" type="text" class="mt-1 block w-full" :value="old('cislo_hospodarstvi', $kun->cislo_hospodarstvi ?? '')" />
                    <x-input-error :messages="$errors->get('cislo_hospodarstvi')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="majitel_jmeno_adresa" :value="'Majitel (jméno + adresa)'" />
                <textarea id="majitel_jmeno_adresa" name="majitel_jmeno_adresa" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('majitel_jmeno_adresa', $kun->majitel_jmeno_adresa ?? '') }}</textarea>
                <x-input-error :messages="$errors->get('majitel_jmeno_adresa')" class="mt-2" />
            </div>
        </section>
    </div>

    <div class="flex items-center gap-3">
        <x-primary-button>{{ $isEdit ? 'Uložit změny' : 'Vytvořit koně' }}</x-primary-button>
        <a href="{{ route('kone.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Zpět na přehled</a>
    </div>
</form>
