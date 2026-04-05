<x-guest-layout>
    <div class="space-y-6">
        <div>
            <p class="section-eyebrow">Registrace</p>
            <h1 class="mt-3 text-3xl text-on-surface dark:text-[#e5e2dd]">Vytvořte si účet pro správu jezdců, koní a přihlášek</h1>
            <p class="mt-3 text-sm leading-6 text-on-surface-variant dark:text-[#c3c8bb]">Účet slouží jako základ pro všechny další registrace. Osoby a koně pak už jen vybíráte z uloženého seznamu.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="jmeno">Jméno</label>
                    <input id="jmeno" type="text" name="jmeno" :value="old('jmeno')" required autofocus autocomplete="given-name" class="field-shell" />
                    <p class="mt-1 text-xs text-error" x-data="{ messages: {{ json_encode($errors->get('jmeno')) }} }" x-show="messages.length > 0" x-text="messages[0]"></p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="prijmeni">Příjmení</label>
                    <input id="prijmeni" type="text" name="prijmeni" :value="old('prijmeni')" required autocomplete="family-name" class="field-shell" />
                    <p class="mt-1 text-xs text-error" x-data="{ messages: {{ json_encode($errors->get('prijmeni')) }} }" x-show="messages.length > 0" x-text="messages[0]"></p>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="datum_narozeni">Datum narození</label>
                    <input id="datum_narozeni" type="date" name="datum_narozeni" :value="old('datum_narozeni')" autocomplete="bday" class="field-shell" />
                    <p class="mt-1 text-xs text-error" x-data="{ messages: {{ json_encode($errors->get('datum_narozeni')) }} }" x-show="messages.length > 0" x-text="messages[0]"></p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="pohlavi">Pohlaví</label>
                    <select id="pohlavi" name="pohlavi" class="field-shell">
                        <option value="">Neuvedeno</option>
                        <option value="F" @selected(old('pohlavi') === 'F')>Žena</option>
                        <option value="M" @selected(old('pohlavi') === 'M')>Muž</option>
                    </select>
                    <p class="mt-1 text-xs text-error" x-data="{ messages: {{ json_encode($errors->get('pohlavi')) }} }" x-show="messages.length > 0" x-text="messages[0]"></p>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="telefon">Telefon</label>
                    <input id="telefon" type="text" name="telefon" :value="old('telefon')" autocomplete="tel" class="field-shell" />
                    <p class="mt-1 text-xs text-error" x-data="{ messages: {{ json_encode($errors->get('telefon')) }} }" x-show="messages.length > 0" x-text="messages[0]"></p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="email">E-mail</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" class="field-shell" />
                    <p class="mt-1 text-xs text-error" x-data="{ messages: {{ json_encode($errors->get('email')) }} }" x-show="messages.length > 0" x-text="messages[0]"></p>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="password">Heslo</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" class="field-shell" />
                    <p class="mt-1 text-xs text-error" x-data="{ messages: {{ json_encode($errors->get('password')) }} }" x-show="messages.length > 0" x-text="messages[0]"></p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="password_confirmation">Potvrzení hesla</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="field-shell" />
                    <p class="mt-1 text-xs text-error" x-data="{ messages: {{ json_encode($errors->get('password_confirmation')) }} }" x-show="messages.length > 0" x-text="messages[0]"></p>
                </div>
            </div>

            <label for="gdpr_souhlas" class="flex items-start gap-3 rounded-[1rem] border border-outline-variant/30 dark:border-[#43493e]/30 bg-surface-container-lowest/60 dark:bg-[#2a2a27]/60 px-4 py-4 text-sm leading-6 text-on-surface dark:text-[#e5e2dd]">
                <input id="gdpr_souhlas" type="checkbox" name="gdpr_souhlas" value="1" class="mt-1 rounded border-outline-variant text-primary focus:ring-primary" @checked(old('gdpr_souhlas')) required>
                <span>Souhlasím se zpracováním osobních údajů pro účely evidence účastníků a přihlášek podle pravidel platformy.</span>
            </label>
            <p class="mt-1 text-xs text-error" x-data="{ messages: {{ json_encode($errors->get('gdpr_souhlas')) }} }" x-show="messages.length > 0" x-text="messages[0]"></p>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">
                    Už účet máte?
                    <a class="text-secondary dark:text-secondary-fixed-dim underline underline-offset-4" href="{{ route('login') }}">Přihlásit se</a>
                </p>

                <button type="submit" class="button-primary w-full">
                    Registrovat účet
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
