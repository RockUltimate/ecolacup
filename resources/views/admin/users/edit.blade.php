<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-on-surface dark:text-[#e5e2dd] leading-tight">Admin • Uživatel • {{ $managedUser->prijmeni }} {{ $managedUser->jmeno }}</h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-primary dark:text-inverse-primary hover:underline underline">Zpět na uživatele</a>
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
                        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Jméno</label>
                        <input id="jmeno" name="jmeno" type="text" class="field-shell mt-1 block w-full" value="{{ old('jmeno', $managedUser->jmeno) }}" required />
                        <x-input-error :messages="$errors->get('jmeno')" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Příjmení</label>
                        <input id="prijmeni" name="prijmeni" type="text" class="field-shell mt-1 block w-full" value="{{ old('prijmeni', $managedUser->prijmeni) }}" required />
                        <x-input-error :messages="$errors->get('prijmeni')" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">E-mail</label>
                        <input id="email" name="email" type="email" class="field-shell mt-1 block w-full" value="{{ old('email', $managedUser->email) }}" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Telefon</label>
                        <input id="telefon" name="telefon" type="text" class="field-shell mt-1 block w-full" value="{{ old('telefon', $managedUser->telefon) }}" />
                        <x-input-error :messages="$errors->get('telefon')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Nové heslo (volitelné)</label>
                        <input id="password" name="password" type="password" class="field-shell mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Potvrzení hesla</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="field-shell mt-1 block w-full" />
                    </div>
                </div>

                <label for="is_admin" class="inline-flex items-center">
                    <input id="is_admin" type="checkbox" name="is_admin" value="1" class="rounded border-outline-variant dark:border-[#43493e] text-primary shadow-sm focus:ring-primary" @checked(old('is_admin', $managedUser->is_admin))>
                    <span class="ms-2 text-sm text-on-surface dark:text-[#e5e2dd]">Administrátorský přístup</span>
                </label>

                <div class="text-sm text-on-surface-variant dark:text-[#c3c8bb]">
                    Osoby: {{ $managedUser->osoby_count }} • Koně: {{ $managedUser->kone_count }} • Přihlášky: {{ $managedUser->prihlasky_count }}
                </div>

                <div>
                    <button type="submit" class="button-primary">Uložit uživatele</button>
                </div>
            </form>
            <div class="panel p-5 space-y-3">
                <h3 class="text-base font-semibold text-on-surface dark:text-[#e5e2dd]">GDPR nástroje</h3>
                <a href="{{ route('admin.users.gdpr-export', $managedUser) }}" class="inline-flex text-sm text-primary dark:text-inverse-primary hover:underline underline">
                    Exportovat uživatelská data (CSV)
                </a>
                <form method="POST" action="{{ route('admin.users.purge', $managedUser) }}" onsubmit="return confirm('Opravdu trvale odstranit uživatele a všechna jeho data? Tato akce je nevratná.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex text-sm text-error hover:underline underline">Trvale odstranit uživatele (purge)</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
