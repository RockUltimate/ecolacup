<x-guest-layout>
    <div class="space-y-6">
        <div>
            <p class="section-eyebrow">Obnova hesla</p>
            <h1 class="mt-3 text-3xl text-[#20392c]">Pošleme vám odkaz pro nastavení nového hesla</h1>
            <p class="mt-3 text-sm leading-6 text-gray-600">Zadejte e-mail použitý při registraci. Odkaz dorazí do schránky a umožní bezpečně nastavit nové heslo.</p>
        </div>

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" :value="'E-mail'" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('login') }}" class="text-sm text-[#7b5230] underline underline-offset-4">Zpět na přihlášení</a>
                <x-primary-button>
                    Poslat odkaz pro reset
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
