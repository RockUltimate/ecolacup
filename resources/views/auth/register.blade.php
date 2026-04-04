<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="jmeno" :value="'Jméno'" />
            <x-text-input id="jmeno" class="block mt-1 w-full" type="text" name="jmeno" :value="old('jmeno')" required autofocus autocomplete="given-name" />
            <x-input-error :messages="$errors->get('jmeno')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="prijmeni" :value="'Příjmení'" />
            <x-text-input id="prijmeni" class="block mt-1 w-full" type="text" name="prijmeni" :value="old('prijmeni')" required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('prijmeni')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="datum_narozeni" :value="'Datum narození'" />
            <x-text-input id="datum_narozeni" class="block mt-1 w-full" type="date" name="datum_narozeni" :value="old('datum_narozeni')" autocomplete="bday" />
            <x-input-error :messages="$errors->get('datum_narozeni')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="pohlavi" :value="'Pohlaví'" />
            <select id="pohlavi" name="pohlavi" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">Neuvedeno</option>
                <option value="F" @selected(old('pohlavi') === 'F')>Žena</option>
                <option value="M" @selected(old('pohlavi') === 'M')>Muž</option>
            </select>
            <x-input-error :messages="$errors->get('pohlavi')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="telefon" :value="'Telefon'" />
            <x-text-input id="telefon" class="block mt-1 w-full" type="text" name="telefon" :value="old('telefon')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('telefon')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="'E-mail'" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="'Heslo'" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="'Potvrzení hesla'" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label for="gdpr_souhlas" class="inline-flex items-center">
                <input id="gdpr_souhlas" type="checkbox" name="gdpr_souhlas" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('gdpr_souhlas')) required>
                <span class="ms-2 text-sm text-gray-600">Souhlasím se zpracováním osobních údajů (GDPR)</span>
            </label>
            <x-input-error :messages="$errors->get('gdpr_souhlas')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                Již máte účet?
            </a>

            <x-primary-button class="ms-4">
                Registrovat
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
