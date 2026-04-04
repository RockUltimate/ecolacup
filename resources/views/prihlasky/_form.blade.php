@php
    $isEdit = isset($prihlaska);
    $selectedMoznosti = old('moznosti', $isEdit ? $prihlaska->polozky->pluck('moznost_id')->all() : []);
    $selectedUstajeni = old('ustajeni', $isEdit ? $prihlaska->ustajeniChoices->pluck('ustajeni_id')->all() : []);
@endphp

<form method="POST" action="{{ $isEdit ? route('prihlasky.update', $prihlaska) : route('prihlasky.store', $udalost) }}" class="space-y-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="osoba_id" :value="'Osoba'" />
            <select id="osoba_id" name="osoba_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" {{ $isEdit ? 'disabled' : '' }} required>
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
            <select id="kun_id" name="kun_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" {{ $isEdit ? 'disabled' : '' }} required>
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

    <div>
        <h3 class="font-semibold text-gray-900 mb-2">Disciplíny</h3>
        <div class="space-y-2">
            @foreach($udalost->moznosti as $moznost)
                <label class="inline-flex items-center w-full p-2 border border-gray-200 rounded-md">
                    <input type="checkbox" name="moznosti[]" value="{{ $moznost->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
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
                <label class="inline-flex items-center w-full p-2 border border-gray-200 rounded-md">
                    <input type="checkbox" name="ustajeni[]" value="{{ $item->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        @checked(in_array($item->id, array_map('intval', $selectedUstajeni), true))>
                    <span class="ms-2 text-sm text-gray-700">{{ $item->nazev }} ({{ $item->typ }}) — {{ number_format((float)$item->cena, 2, ',', ' ') }} Kč</span>
                </label>
            @endforeach
        </div>
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

    <div class="flex items-center gap-3">
        <x-primary-button>{{ $isEdit ? 'Uložit přihlášku' : 'Odeslat přihlášku' }}</x-primary-button>
        <a href="{{ $isEdit ? route('prihlasky.show', $prihlaska) : route('udalosti.show', $udalost) }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Zpět</a>
    </div>
</form>
