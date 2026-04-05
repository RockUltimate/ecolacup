<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-on-surface dark:text-[#e5e2dd] leading-tight">
                Admin • Členství • {{ $membership->osoba?->prijmeni }} {{ $membership->osoba?->jmeno }}
            </h2>
            <a href="{{ route('admin.clenstvi.index') }}" class="text-sm text-primary dark:text-inverse-primary hover:underline underline">Zpět na členství</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-flash-message />
            <form method="POST" action="{{ route('admin.clenstvi.update', $membership) }}" class="panel p-5 space-y-4" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Evidenční číslo</label>
                        <input id="evidencni_cislo" name="evidencni_cislo" type="text" class="field-shell mt-1 block w-full" value="{{ old('evidencni_cislo', $membership->evidencni_cislo) }}" />
                        <x-input-error :messages="$errors->get('evidencni_cislo')" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Rok</label>
                        <input id="rok" name="rok" type="number" class="field-shell mt-1 block w-full" value="{{ old('rok', $membership->rok) }}" required />
                        <x-input-error :messages="$errors->get('rok')" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Cena (Kč)</label>
                        <input id="cena" name="cena" type="number" step="0.01" class="field-shell mt-1 block w-full" value="{{ old('cena', $membership->cena) }}" required />
                        <x-input-error :messages="$errors->get('cena')" class="mt-2" />
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">E-mail</label>
                        <input id="email" name="email" type="email" class="field-shell mt-1 block w-full" value="{{ old('email', $membership->email) }}" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Telefon</label>
                        <input id="telefon" name="telefon" type="text" class="field-shell mt-1 block w-full" value="{{ old('telefon', $membership->telefon) }}" />
                        <x-input-error :messages="$errors->get('telefon')" class="mt-2" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant dark:text-[#c3c8bb]">Nahrát sken přihlášky (jpg/png/webp/pdf)</label>
                    <input
                        id="sken_prihlaska_upload"
                        name="sken_prihlaska_upload"
                        type="file"
                        accept=".jpg,.jpeg,.png,.webp,.pdf"
                        class="mt-1 block w-full text-sm text-on-surface dark:text-[#e5e2dd] file:mr-3 file:rounded-md file:border-0 file:bg-[#3d6b4f] file:px-3 file:py-2 file:text-white hover:file:bg-[#31563f]"
                    >
                    <x-input-error :messages="$errors->get('sken_prihlaska_upload')" class="mt-2" />
                    @if($membership->sken_prihlaska)
                        <p class="mt-2 text-sm text-on-surface-variant dark:text-[#c3c8bb]">
                            Aktuální soubor:
                            <a href="{{ asset('storage/'.$membership->sken_prihlaska) }}" target="_blank" rel="noopener" class="text-primary dark:text-inverse-primary hover:underline underline">
                                zobrazit sken
                            </a>
                        </p>
                    @endif
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <label for="aktivni" class="inline-flex items-center">
                        <input id="aktivni" type="checkbox" name="aktivni" value="1" class="rounded border-outline-variant dark:border-[#43493e] text-primary shadow-sm focus:ring-primary" @checked(old('aktivni', $membership->aktivni))>
                        <span class="ms-2 text-sm text-on-surface dark:text-[#e5e2dd]">Aktivní členství</span>
                    </label>
                    <label for="souhlas_gdpr" class="inline-flex items-center">
                        <input id="souhlas_gdpr" type="checkbox" name="souhlas_gdpr" value="1" class="rounded border-outline-variant dark:border-[#43493e] text-primary shadow-sm focus:ring-primary" @checked(old('souhlas_gdpr', $membership->souhlas_gdpr))>
                        <span class="ms-2 text-sm text-on-surface dark:text-[#e5e2dd]">Souhlas GDPR</span>
                    </label>
                    <label for="souhlas_email" class="inline-flex items-center">
                        <input id="souhlas_email" type="checkbox" name="souhlas_email" value="1" class="rounded border-outline-variant dark:border-[#43493e] text-primary shadow-sm focus:ring-primary" @checked(old('souhlas_email', $membership->souhlas_email))>
                        <span class="ms-2 text-sm text-on-surface dark:text-[#e5e2dd]">Souhlas se zasíláním e-mailů</span>
                    </label>
                    <label for="souhlas_zverejneni" class="inline-flex items-center">
                        <input id="souhlas_zverejneni" type="checkbox" name="souhlas_zverejneni" value="1" class="rounded border-outline-variant dark:border-[#43493e] text-primary shadow-sm focus:ring-primary" @checked(old('souhlas_zverejneni', $membership->souhlas_zverejneni))>
                        <span class="ms-2 text-sm text-on-surface dark:text-[#e5e2dd]">Souhlas se zveřejněním</span>
                    </label>
                </div>
                <div>
                    <button type="submit" class="button-primary">Uložit členství</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
