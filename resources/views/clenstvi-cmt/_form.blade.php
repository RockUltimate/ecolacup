@php
    $isEdit = isset($clenstvi);
@endphp

<form method="POST" action="{{ $isEdit ? route('clenstvi-cmt.update', $clenstvi) : route('clenstvi-cmt.store') }}" class="space-y-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="osoba_id" :value="'Osoba'" />
            <select id="osoba_id" name="osoba_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">Vyberte osobu</option>
                @foreach($osoby as $osoba)
                    <option value="{{ $osoba->id }}" @selected((int) old('osoba_id', $clenstvi->osoba_id ?? 0) === (int) $osoba->id)>
                        {{ $osoba->prijmeni }} {{ $osoba->jmeno }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('osoba_id')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="typ_clenstvi" :value="'Typ členství'" />
            <x-text-input id="typ_clenstvi" name="typ_clenstvi" type="text" class="mt-1 block w-full" :value="old('typ_clenstvi', $clenstvi->typ_clenstvi ?? '')" required />
            <x-input-error :messages="$errors->get('typ_clenstvi')" class="mt-2" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-input-label for="rok" :value="'Rok'" />
            <x-text-input id="rok" name="rok" type="number" class="mt-1 block w-full" :value="old('rok', $clenstvi->rok ?? now()->year)" required />
            <x-input-error :messages="$errors->get('rok')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="cena" :value="'Cena (Kč)'" />
            <x-text-input id="cena" name="cena" type="number" step="0.01" class="mt-1 block w-full" :value="old('cena', $clenstvi->cena ?? '0')" required />
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
        <x-input-label for="sken_prihlaska" :value="'Cesta ke skenu přihlášky'" />
        <x-text-input id="sken_prihlaska" name="sken_prihlaska" type="text" class="mt-1 block w-full" :value="old('sken_prihlaska', $clenstvi->sken_prihlaska ?? '')" />
        <x-input-error :messages="$errors->get('sken_prihlaska')" class="mt-2" />
    </div>

    <div class="space-y-2">
        <label for="aktivni" class="inline-flex items-center">
            <input id="aktivni" type="checkbox" name="aktivni" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('aktivni', $clenstvi->aktivni ?? true))>
            <span class="ms-2 text-sm text-gray-700">Aktivní členství</span>
        </label>
        <label for="souhlas_gdpr" class="inline-flex items-center">
            <input id="souhlas_gdpr" type="checkbox" name="souhlas_gdpr" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('souhlas_gdpr', $clenstvi->souhlas_gdpr ?? false))>
            <span class="ms-2 text-sm text-gray-700">Souhlas GDPR</span>
        </label>
        <label for="souhlas_email" class="inline-flex items-center">
            <input id="souhlas_email" type="checkbox" name="souhlas_email" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('souhlas_email', $clenstvi->souhlas_email ?? false))>
            <span class="ms-2 text-sm text-gray-700">Souhlas se zasíláním e-mailů</span>
        </label>
    </div>

    <div class="flex items-center gap-3">
        <x-primary-button>{{ $isEdit ? 'Uložit členství' : 'Vytvořit členství' }}</x-primary-button>
        <a href="{{ route('clenstvi-cmt.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Zpět</a>
    </div>
</form>
