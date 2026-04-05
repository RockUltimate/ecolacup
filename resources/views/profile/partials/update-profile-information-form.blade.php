<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Údaje účtu
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Upravte své osobní údaje a kontaktní e-mail.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('ucet.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="jmeno" :value="'Jméno'" />
            <x-text-input id="jmeno" name="jmeno" type="text" class="mt-1 block w-full" :value="old('jmeno', $user->jmeno)" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('jmeno')" />
        </div>

        <div>
            <x-input-label for="prijmeni" :value="'Příjmení'" />
            <x-text-input id="prijmeni" name="prijmeni" type="text" class="mt-1 block w-full" :value="old('prijmeni', $user->prijmeni)" required />
            <x-input-error class="mt-2" :messages="$errors->get('prijmeni')" />
        </div>

        <div>
            <x-input-label for="datum_narozeni" :value="'Datum narození'" />
            <x-text-input id="datum_narozeni" name="datum_narozeni" type="date" class="mt-1 block w-full" :value="old('datum_narozeni', optional($user->datum_narozeni)->format('Y-m-d'))" />
            <x-input-error class="mt-2" :messages="$errors->get('datum_narozeni')" />
        </div>

        <div>
            <x-input-label for="pohlavi" :value="'Pohlaví'" />
            <select id="pohlavi" name="pohlavi" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">Neuvedeno</option>
                <option value="F" @selected(old('pohlavi', $user->pohlavi) === 'F')>Žena</option>
                <option value="M" @selected(old('pohlavi', $user->pohlavi) === 'M')>Muž</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('pohlavi')" />
        </div>

        <div>
            <x-input-label for="telefon" :value="'Telefon'" />
            <x-text-input id="telefon" name="telefon" type="text" class="mt-1 block w-full" :value="old('telefon', $user->telefon)" />
            <x-input-error class="mt-2" :messages="$errors->get('telefon')" />
        </div>

        <div>
            <x-input-label for="email" :value="'E-mail'" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        E-mail není ověřen.

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Odeslat ověřovací e-mail znovu
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            Nový ověřovací odkaz byl odeslán.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <label for="gdpr_souhlas" class="inline-flex items-center">
                <input id="gdpr_souhlas" type="checkbox" name="gdpr_souhlas" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('gdpr_souhlas', $user->gdpr_souhlas)) required>
                <span class="ms-2 text-sm text-gray-600">Souhlasím se zpracováním osobních údajů (GDPR)</span>
            </label>
            <x-input-error class="mt-2" :messages="$errors->get('gdpr_souhlas')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Uložit</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >Uloženo.</p>
            @endif
        </div>
    </form>
</section>
