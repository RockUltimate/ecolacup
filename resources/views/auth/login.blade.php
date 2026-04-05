<x-guest-layout>
    <div class="space-y-6">
        <div>
            <p class="section-eyebrow">Přihlášení</p>
            <h1 class="mt-3 text-3xl text-[#20392c]">Vraťte se do svého účtu</h1>
            <p class="mt-3 text-sm leading-6 text-gray-600">Po přihlášení navážete na uložené osoby, koně i rozpracované přihlášky.</p>
        </div>

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" :value="'E-mail'" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <div class="flex items-center justify-between gap-4">
                    <x-input-label for="password" :value="'Heslo'" />
                    @if (Route::has('password.request'))
                        <a class="text-sm text-[#7b5230] underline underline-offset-4" href="{{ route('password.request') }}">
                            Zapomenuté heslo?
                        </a>
                    @endif
                </div>

                <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <label for="remember_me" class="flex items-center gap-3 rounded-[1rem] border border-[#eadfcc] bg-white/60 px-4 py-3 text-sm text-gray-700">
                <input id="remember_me" type="checkbox" class="rounded border-[#ccb28f] text-[#3d6b4f] focus:ring-[#3d6b4f]" name="remember">
                <span>Zůstat přihlášený i při další návštěvě.</span>
            </label>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-gray-600">
                    Nemáte účet?
                    <a class="text-[#7b5230] underline underline-offset-4" href="{{ route('register') }}">Vytvořit nový účet</a>
                </p>
                <x-primary-button>
                    Přihlásit se
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
