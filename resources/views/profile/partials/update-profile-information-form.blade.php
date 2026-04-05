<section>
    <header>
        <h2 class="text-lg font-medium text-on-surface dark:text-[#e5e2dd]">
            Údaje účtu
        </h2>

        <p class="mt-1 text-sm text-on-surface-variant dark:text-[#c3c8bb]">
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
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="jmeno">Jméno</label>
            <input id="jmeno" name="jmeno" type="text" class="field-shell mt-1 block w-full" value="{{ old('jmeno', $user->jmeno) }}" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('jmeno')" />
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="prijmeni">Příjmení</label>
            <input id="prijmeni" name="prijmeni" type="text" class="field-shell mt-1 block w-full" value="{{ old('prijmeni', $user->prijmeni) }}" required />
            <x-input-error class="mt-2" :messages="$errors->get('prijmeni')" />
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="datum_narozeni">Datum narození</label>
            <input id="datum_narozeni" name="datum_narozeni" type="date" class="field-shell mt-1 block w-full" value="{{ old('datum_narozeni', optional($user->datum_narozeni)->format('Y-m-d')) }}" />
            <x-input-error class="mt-2" :messages="$errors->get('datum_narozeni')" />
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="pohlavi">Pohlaví</label>
            <select id="pohlavi" name="pohlavi" class="mt-1 block w-full border-outline-variant dark:border-[#43493e] focus:border-primary focus:ring-primary rounded-md shadow-sm">
                <option value="">Neuvedeno</option>
                <option value="F" @selected(old('pohlavi', $user->pohlavi) === 'F')>Žena</option>
                <option value="M" @selected(old('pohlavi', $user->pohlavi) === 'M')>Muž</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('pohlavi')" />
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="telefon">Telefon</label>
            <input id="telefon" name="telefon" type="text" class="field-shell mt-1 block w-full" value="{{ old('telefon', $user->telefon) }}" />
            <x-input-error class="mt-2" :messages="$errors->get('telefon')" />
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="email">E-mail</label>
            <input id="email" name="email" type="email" class="field-shell mt-1 block w-full" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-on-surface dark:text-[#e5e2dd]">
                        E-mail není ověřen.

                        <button form="send-verification" class="underline text-sm text-on-surface-variant dark:text-[#c3c8bb] hover:text-on-surface dark:hover:text-[#e5e2dd] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Odeslat ověřovací e-mail znovu
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-primary dark:text-inverse-primary">
                            Nový ověřovací odkaz byl odeslán.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <label for="gdpr_souhlas" class="inline-flex items-center">
                <input id="gdpr_souhlas" type="checkbox" name="gdpr_souhlas" value="1" class="rounded border-outline-variant dark:border-[#43493e] text-primary shadow-sm focus:ring-primary" @checked(old('gdpr_souhlas', $user->gdpr_souhlas)) required>
                <span class="ms-2 text-sm text-on-surface-variant dark:text-[#c3c8bb]">Souhlasím se zpracováním osobních údajů (GDPR)</span>
            </label>
            <x-input-error class="mt-2" :messages="$errors->get('gdpr_souhlas')" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="button-primary">Uložit</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-on-surface-variant dark:text-[#c3c8bb]"
                >Uloženo.</p>
            @endif
        </div>
    </form>
</section>
