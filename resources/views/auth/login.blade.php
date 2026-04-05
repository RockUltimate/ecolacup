<x-guest-layout>
    <div class="space-y-6">
        <div>
            <p class="section-eyebrow">Přihlášení</p>
            <h1 class="mt-3 text-3xl text-on-surface dark:text-[#e5e2dd]">Vraťte se do svého účtu</h1>
            <p class="mt-3 text-sm leading-6 text-on-surface-variant dark:text-[#c3c8bb]">Po přihlášení navážete na uložené osoby, koně i rozpracované přihlášky.</p>
        </div>

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="email">E-mail</label>
                <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" class="field-shell" />
                <p class="mt-1 text-xs text-error" x-data="{ messages: {{ json_encode($errors->get('email')) }} }" x-show="messages.length > 0" x-text="messages[0]"></p>
            </div>

            <div>
                <div class="flex items-center justify-between gap-4">
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="password">Heslo</label>
                    @if (Route::has('password.request'))
                        <a class="text-sm text-secondary dark:text-secondary-fixed-dim underline underline-offset-4" href="{{ route('password.request') }}">
                            Zapomenuté heslo?
                        </a>
                    @endif
                </div>

                <input id="password" type="password" name="password" required autocomplete="current-password" class="field-shell" />
                <p class="mt-1 text-xs text-error" x-data="{ messages: {{ json_encode($errors->get('password')) }} }" x-show="messages.length > 0" x-text="messages[0]"></p>
            </div>

            <label for="remember_me" class="flex items-center gap-3 rounded-[1rem] border border-outline-variant/30 dark:border-[#43493e]/30 bg-surface-container-lowest/60 dark:bg-[#2a2a27]/60 px-4 py-3 text-sm text-on-surface dark:text-[#e5e2dd]">
                <input id="remember_me" type="checkbox" class="rounded border-outline-variant text-primary focus:ring-primary" name="remember">
                <span>Zůstat přihlášený i při další návštěvě.</span>
            </label>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">
                    Nemáte účet?
                    <a class="text-secondary dark:text-secondary-fixed-dim underline underline-offset-4" href="{{ route('register') }}">Vytvořit nový účet</a>
                </p>
                <button type="submit" class="button-primary w-full">
                    Přihlásit se
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
