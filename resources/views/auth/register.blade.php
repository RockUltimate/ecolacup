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
                    <x-text-input id="datum_narozeni" type="text" name="datum_narozeni" :value="old('datum_narozeni')" placeholder="DD.MM.RRRR" autocomplete="bday" />
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
                    <div x-data="{ show: false }" class="relative">
                        <x-text-input id="password" :type="'password'" x-bind:type="show ? 'text' : 'password'" name="password" required autocomplete="new-password" class="pr-11" />
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="'Potvrzení hesla'" />
                    <div x-data="{ show: false }" class="relative">
                        <x-text-input id="password_confirmation" :type="'password'" x-bind:type="show ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" class="pr-11" />
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <label for="gdpr_souhlas" class="flex items-start gap-3 rounded-[1rem] border border-[#eadfcc] bg-white/60 px-4 py-4 text-sm leading-6 text-gray-700">
                <input id="gdpr_souhlas" type="checkbox" name="gdpr_souhlas" value="1" class="mt-1 rounded border-[#ccb28f] text-[#3d6b4f] focus:ring-[#3d6b4f]" @checked(old('gdpr_souhlas'))>
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
