@php
    $isEdit = isset($udalost);
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.udalosti.update', $udalost) : route('admin.udalosti.store') }}" class="space-y-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div>
        <x-input-label for="nazev" :value="'Název'" />
        <x-text-input id="nazev" name="nazev" type="text" class="mt-1 block w-full" :value="old('nazev', $udalost->nazev ?? '')" required />
        <x-input-error :messages="$errors->get('nazev')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="misto" :value="'Místo'" />
        <x-text-input id="misto" name="misto" type="text" class="mt-1 block w-full" :value="old('misto', $udalost->misto ?? '')" required />
        <x-input-error :messages="$errors->get('misto')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-input-label for="datum_zacatek" :value="'Datum začátku'" />
            <x-text-input id="datum_zacatek" name="datum_zacatek" type="date" class="mt-1 block w-full" :value="old('datum_zacatek', isset($udalost) && $udalost->datum_zacatek ? $udalost->datum_zacatek->format('Y-m-d') : '')" required />
            <x-input-error :messages="$errors->get('datum_zacatek')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="datum_konec" :value="'Datum konce'" />
            <x-text-input id="datum_konec" name="datum_konec" type="date" class="mt-1 block w-full" :value="old('datum_konec', isset($udalost) && $udalost->datum_konec ? $udalost->datum_konec->format('Y-m-d') : '')" required />
            <x-input-error :messages="$errors->get('datum_konec')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="uzavierka_prihlasek" :value="'Uzávěrka přihlášek'" />
            <x-text-input id="uzavierka_prihlasek" name="uzavierka_prihlasek" type="date" class="mt-1 block w-full" :value="old('uzavierka_prihlasek', isset($udalost) && $udalost->uzavierka_prihlasek ? $udalost->uzavierka_prihlasek->format('Y-m-d') : '')" required />
            <x-input-error :messages="$errors->get('uzavierka_prihlasek')" class="mt-2" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="kapacita" :value="'Kapacita (volitelně)'" />
            <x-text-input id="kapacita" name="kapacita" type="number" min="1" class="mt-1 block w-full" :value="old('kapacita', $udalost->kapacita ?? '')" />
            <x-input-error :messages="$errors->get('kapacita')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="propozice_pdf" :value="'Cesta k propozicím (volitelně)'" />
            <x-text-input id="propozice_pdf" name="propozice_pdf" type="text" class="mt-1 block w-full" :value="old('propozice_pdf', $udalost->propozice_pdf ?? '')" />
            <x-input-error :messages="$errors->get('propozice_pdf')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="popis" :value="'Popis'" />
        <textarea id="popis" name="popis" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('popis', $udalost->popis ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('popis')" class="mt-2" />
    </div>

    <div>
        <label for="aktivni" class="inline-flex items-center">
            <input id="aktivni" type="checkbox" name="aktivni" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('aktivni', $udalost->aktivni ?? true))>
            <span class="ms-2 text-sm text-gray-700">Aktivní událost</span>
        </label>
    </div>

    <div class="flex items-center gap-3">
        <x-primary-button>{{ $isEdit ? 'Uložit událost' : 'Vytvořit událost' }}</x-primary-button>
        <a href="{{ route('admin.udalosti.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Zpět na seznam</a>
    </div>
</form>
