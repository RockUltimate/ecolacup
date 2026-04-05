<x-guest-layout>
    <div class="space-y-6">
        <div>
            <p class="section-eyebrow">Obnova hesla</p>
            <h1 class="mt-3 text-3xl text-on-surface dark:text-[#e5e2dd]">Pošleme vám odkaz pro nastavení nového hesla</h1>
            <p class="mt-3 text-sm leading-6 text-on-surface-variant dark:text-[#c3c8bb]">Zadejte e-mail použitý při registraci. Odkaz dorazí do schránky a umožní bezpečně nastavit nové heslo.</p>
        </div>

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="email">E-mail</label>
                <input id="email" type="email" name="email" :value="old('email')" required autofocus class="field-shell" />
                <p class="mt-1 text-xs text-error" x-data="{ messages: {{ json_encode($errors->get('email')) }} }" x-show="messages.length > 0" x-text="messages[0]"></p>
            </div>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('login') }}" class="text-sm text-secondary dark:text-secondary-fixed-dim underline underline-offset-4">Zpět na přihlášení</a>
                <button type="submit" class="button-primary w-full">
                    Poslat odkaz pro reset
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
