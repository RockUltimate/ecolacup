<x-guest-layout>
    <div class="space-y-6">
        <div>
            <p class="section-eyebrow">Nové heslo</p>
            <h1 class="mt-3 text-3xl text-on-surface dark:text-[#e5e2dd]">Nastavte si nové přístupové údaje</h1>
            <p class="mt-3 text-sm leading-6 text-on-surface-variant dark:text-[#c3c8bb]">Po uložení nového hesla se můžete znovu přihlásit a pokračovat v práci s přihláškami.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="email">E-mail</label>
                <input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" class="field-shell" />
                <p class="mt-1 text-xs text-error" x-data="{ messages: {{ json_encode($errors->get('email')) }} }" x-show="messages.length > 0" x-text="messages[0]"></p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="password">Nové heslo</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" class="field-shell" />
                    <p class="mt-1 text-xs text-error" x-data="{ messages: {{ json_encode($errors->get('password')) }} }" x-show="messages.length > 0" x-text="messages[0]"></p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="password_confirmation">Potvrzení hesla</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="field-shell" />
                    <p class="mt-1 text-xs text-error" x-data="{ messages: {{ json_encode($errors->get('password_confirmation')) }} }" x-show="messages.length > 0" x-text="messages[0]"></p>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="button-primary w-full">
                    Uložit nové heslo
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
