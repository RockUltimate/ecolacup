<x-guest-layout>
    <div class="mb-4 text-sm text-on-surface-variant dark:text-[#c3c8bb]">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]" for="password">{{ __('Password') }}</label>

            <input id="password" class="field-shell"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <p class="mt-1 text-xs text-error" x-data="{ messages: {{ json_encode($errors->get('password')) }} }" x-show="messages.length > 0" x-text="messages[0]"></p>
        </div>

        <div class="flex justify-end mt-4">
            <button type="submit" class="button-primary w-full">
                {{ __('Confirm') }}
            </button>
        </div>
    </form>
</x-guest-layout>
