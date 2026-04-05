@php
    $isEdit = isset($osoba);
@endphp

<form method="POST" action="{{ $isEdit ? route('osoby.update', $osoba) : route('osoby.store') }}" class="space-y-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div>
        <x-input-label for="jmeno" :value="'Jméno'" />
        <x-text-input id="jmeno" name="jmeno" type="text" class="mt-1 block w-full" :value="old('jmeno', $osoba->jmeno ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('jmeno')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="prijmeni" :value="'Příjmení'" />
        <x-text-input id="prijmeni" name="prijmeni" type="text" class="mt-1 block w-full" :value="old('prijmeni', $osoba->prijmeni ?? '')" required />
        <x-input-error :messages="$errors->get('prijmeni')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="datum_narozeni" :value="'Datum narození'" />
        <x-text-input id="datum_narozeni" name="datum_narozeni" type="text" class="mt-1 block w-full" :value="old('datum_narozeni', isset($osoba) && $osoba->datum_narozeni ? $osoba->datum_narozeni->format('d.m.Y') : '')" placeholder="DD.MM.RRRR" autocomplete="bday" required />
        <x-input-error :messages="$errors->get('datum_narozeni')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="staj" :value="'Stáj'" />
        <x-text-input id="staj" name="staj" type="text" class="mt-1 block w-full" :value="old('staj', $osoba->staj ?? '')" required />
        <x-input-error :messages="$errors->get('staj')" class="mt-2" />
    </div>

    @if($isEdit)
        <div class="space-y-2">
            <label for="gdpr_souhlas" class="inline-flex items-center">
                <input id="gdpr_souhlas" type="checkbox" name="gdpr_souhlas" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('gdpr_souhlas', $osoba->gdpr_souhlas))>
                <span class="ms-2 text-sm text-gray-700">GDPR souhlas aktivní</span>
            </label>
            <label for="gdpr_odvolano" class="inline-flex items-center">
                <input id="gdpr_odvolano" type="checkbox" name="gdpr_odvolano" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500" @checked(old('gdpr_odvolano', $osoba->gdpr_odvolano))>
                <span class="ms-2 text-sm text-gray-700">Odvolat GDPR souhlas</span>
            </label>
        </div>
    @else
        <div>
            <label for="gdpr_souhlas" class="inline-flex items-center">
                <input id="gdpr_souhlas" type="checkbox" name="gdpr_souhlas" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('gdpr_souhlas')) required>
                <span class="ms-2 text-sm text-gray-700">Souhlasím se zpracováním osobních údajů (GDPR)</span>
            </label>
            <x-input-error :messages="$errors->get('gdpr_souhlas')" class="mt-2" />
        </div>
    @endif

    <div class="flex items-center gap-3">
        <x-primary-button>{{ $isEdit ? 'Uložit změny' : 'Vytvořit osobu' }}</x-primary-button>
        <a href="{{ route('osoby.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Zpět na přehled</a>
    </div>
</form>
