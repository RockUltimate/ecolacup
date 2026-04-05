<x-guest-layout>
    <div class="space-y-6">
        <div>
            <p class="section-eyebrow">Nové heslo</p>
            <h1 class="mt-3 text-3xl text-[#20392c]">Nastavte si nové přístupové údaje</h1>
            <p class="mt-3 text-sm leading-6 text-gray-600">Po uložení nového hesla se můžete znovu přihlásit a pokračovat v práci s přihláškami.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-input-label for="email" :value="'E-mail'" />
                <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-input-label for="password" :value="'Nové heslo'" />
                    <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="'Potvrzení hesla'" />
                    <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <x-primary-button>
                    Uložit nové heslo
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
