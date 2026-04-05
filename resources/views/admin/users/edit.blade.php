<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin • Uživatel • {{ $managedUser->prijmeni }} {{ $managedUser->jmeno }}</h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Zpět na uživatele</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-flash-message />
            <form method="POST" action="{{ route('admin.users.update', $managedUser) }}" class="panel p-5 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="jmeno" :value="'Jméno'" />
                        <x-text-input id="jmeno" name="jmeno" type="text" class="mt-1 block w-full" :value="old('jmeno', $managedUser->jmeno)" required />
                        <x-input-error :messages="$errors->get('jmeno')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="prijmeni" :value="'Příjmení'" />
                        <x-text-input id="prijmeni" name="prijmeni" type="text" class="mt-1 block w-full" :value="old('prijmeni', $managedUser->prijmeni)" required />
                        <x-input-error :messages="$errors->get('prijmeni')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" :value="'E-mail'" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $managedUser->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="telefon" :value="'Telefon'" />
                        <x-text-input id="telefon" name="telefon" type="text" class="mt-1 block w-full" :value="old('telefon', $managedUser->telefon)" />
                        <x-input-error :messages="$errors->get('telefon')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="password" :value="'Nové heslo (volitelné)'" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" :value="'Potvrzení hesla'" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                    </div>
                </div>

                <label for="is_admin" class="inline-flex items-center">
                    <input id="is_admin" type="checkbox" name="is_admin" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_admin', $managedUser->is_admin))>
                    <span class="ms-2 text-sm text-gray-700">Administrátorský přístup</span>
                </label>

                <div class="text-sm text-gray-600">
                    Osoby: {{ $managedUser->osoby_count }} • Koně: {{ $managedUser->kone_count }} • Přihlášky: {{ $managedUser->prihlasky_count }}
                </div>

                <div>
                    <x-primary-button>Uložit uživatele</x-primary-button>
                </div>
            </form>
            <div class="panel p-5 space-y-3">
                <h3 class="text-base font-semibold text-gray-900">GDPR nástroje</h3>
                <a href="{{ route('admin.users.gdpr-export', $managedUser) }}" class="inline-flex text-sm text-indigo-600 hover:text-indigo-800 underline">
                    Exportovat uživatelská data (CSV)
                </a>
                <form method="POST" action="{{ route('admin.users.purge', $managedUser) }}" onsubmit="return confirm('Opravdu trvale odstranit uživatele a všechna jeho data? Tato akce je nevratná.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex text-sm text-red-600 hover:text-red-800 underline">Trvale odstranit uživatele (purge)</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
