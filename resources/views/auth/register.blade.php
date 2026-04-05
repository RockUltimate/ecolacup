<x-guest-layout>
    <div class="space-y-6">
        <div>
            <p class="section-eyebrow">Registrace</p>
            <h1 class="mt-3 text-3xl text-[#20392c]">Vytvořte si účet pro správu jezdců, koní a přihlášek</h1>
            <p class="mt-3 text-sm leading-6 text-gray-600">Účet slouží jako základ pro všechny další registrace. Osoby a koně pak už jen vybíráte z uloženého seznamu.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-input-label for="jmeno" :value="'Jméno'" />
                    <x-text-input id="jmeno" type="text" name="jmeno" :value="old('jmeno')" required autofocus autocomplete="given-name" />
                    <x-input-error :messages="$errors->get('jmeno')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="prijmeni" :value="'Příjmení'" />
                    <x-text-input id="prijmeni" type="text" name="prijmeni" :value="old('prijmeni')" required autocomplete="family-name" />
                    <x-input-error :messages="$errors->get('prijmeni')" class="mt-2" />
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-input-label for="datum_narozeni" :value="'Datum narození'" />
                    <x-text-input id="datum_narozeni" type="date" name="datum_narozeni" :value="old('datum_narozeni')" autocomplete="bday" />
                    <x-input-error :messages="$errors->get('datum_narozeni')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="pohlavi" :value="'Pohlaví'" />
                    <select id="pohlavi" name="pohlavi" class="field-shell">
                        <option value="">Neuvedeno</option>
                        <option value="F" @selected(old('pohlavi') === 'F')>Žena</option>
                        <option value="M" @selected(old('pohlavi') === 'M')>Muž</option>
                    </select>
                    <x-input-error :messages="$errors->get('pohlavi')" class="mt-2" />
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-input-label for="telefon" :value="'Telefon'" />
                    <x-text-input id="telefon" type="text" name="telefon" :value="old('telefon')" autocomplete="tel" />
                    <x-input-error :messages="$errors->get('telefon')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" :value="'E-mail'" />
                    <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-input-label for="password" :value="'Heslo'" />
                    <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="'Potvrzení hesla'" />
                    <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <label for="gdpr_souhlas" class="flex items-start gap-3 rounded-[1rem] border border-[#eadfcc] bg-white/60 px-4 py-4 text-sm leading-6 text-gray-700">
                <input id="gdpr_souhlas" type="checkbox" name="gdpr_souhlas" value="1" class="mt-1 rounded border-[#ccb28f] text-[#3d6b4f] focus:ring-[#3d6b4f]" @checked(old('gdpr_souhlas')) required>
                <span>Souhlasím se zpracováním osobních údajů pro účely evidence účastníků a přihlášek podle pravidel platformy.</span>
            </label>
            <x-input-error :messages="$errors->get('gdpr_souhlas')" class="mt-2" />

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-gray-600">
                    Už účet máte?
                    <a class="text-[#7b5230] underline underline-offset-4" href="{{ route('login') }}">Přihlásit se</a>
                </p>

                <x-primary-button>
                    Registrovat účet
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
