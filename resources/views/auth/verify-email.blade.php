<x-guest-layout>
    <div class="space-y-6">
        <div>
            <p class="section-eyebrow">Ověření e-mailu</p>
            <h1 class="mt-3 text-3xl text-[#20392c]">Ještě potvrďte svou e-mailovou adresu</h1>
            <p class="mt-3 text-sm leading-6 text-gray-600">Klikněte na odkaz v e-mailu, který jsme poslali po registraci. Tím se účet aktivuje pro další kroky v aplikaci.</p>
        </div>

        @if (session('status') === 'verification-link-sent')
            <div class="status-note border-emerald-200 bg-emerald-50 text-emerald-800">
                Nový ověřovací e-mail byl právě odeslán.
            </div>
        @endif

        <div class="surface-muted">
            <p class="text-sm leading-6 text-gray-700">Pokud zprávu nevidíte, zkontrolujte i složku hromadné pošty nebo si nechte poslat nový odkaz.</p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-primary-button>
                    Poslat ověřovací e-mail znovu
                </x-primary-button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-[#7b5230] underline underline-offset-4">
                    Odhlásit se
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
