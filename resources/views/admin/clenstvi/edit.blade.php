<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin • Členství • {{ $membership->osoba?->prijmeni }} {{ $membership->osoba?->jmeno }}
            </h2>
            <a href="{{ route('admin.clenstvi.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Zpět na členství</a>
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
                        <x-input-label for="evidencni_cislo" :value="'Evidenční číslo'" />
                        <x-text-input id="evidencni_cislo" name="evidencni_cislo" type="text" class="mt-1 block w-full" :value="old('evidencni_cislo', $membership->evidencni_cislo)" />
                        <x-input-error :messages="$errors->get('evidencni_cislo')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="rok" :value="'Rok'" />
                        <x-text-input id="rok" name="rok" type="number" class="mt-1 block w-full" :value="old('rok', $membership->rok)" required />
                        <x-input-error :messages="$errors->get('rok')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="cena" :value="'Cena (Kč)'" />
                        <x-text-input id="cena" name="cena" type="number" step="0.01" class="mt-1 block w-full" :value="old('cena', $membership->cena)" required />
                        <x-input-error :messages="$errors->get('cena')" class="mt-2" />
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="email" :value="'E-mail'" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $membership->email)" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="telefon" :value="'Telefon'" />
                        <x-text-input id="telefon" name="telefon" type="text" class="mt-1 block w-full" :value="old('telefon', $membership->telefon)" />
                        <x-input-error :messages="$errors->get('telefon')" class="mt-2" />
                    </div>
                </div>
                <div>
                    <x-input-label for="sken_prihlaska_upload" :value="'Nahrát sken přihlášky (jpg/png/webp/pdf)'" />
                    <input
                        id="sken_prihlaska_upload"
                        name="sken_prihlaska_upload"
                        type="file"
                        accept=".jpg,.jpeg,.png,.webp,.pdf"
                        class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:rounded-md file:border-0 file:bg-[#3d6b4f] file:px-3 file:py-2 file:text-white hover:file:bg-[#31563f]"
                    >
                    <x-input-error :messages="$errors->get('sken_prihlaska_upload')" class="mt-2" />
                    @if($membership->sken_prihlaska)
                        <p class="mt-2 text-sm text-gray-600">
                            Aktuální soubor:
                            <a href="{{ asset('storage/'.$membership->sken_prihlaska) }}" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-800 underline">
                                zobrazit sken
                            </a>
                        </p>
                    @endif
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <label for="aktivni" class="inline-flex items-center">
                        <input id="aktivni" type="checkbox" name="aktivni" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('aktivni', $membership->aktivni))>
                        <span class="ms-2 text-sm text-gray-700">Aktivní členství</span>
                    </label>
                    <label for="souhlas_gdpr" class="inline-flex items-center">
                        <input id="souhlas_gdpr" type="checkbox" name="souhlas_gdpr" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('souhlas_gdpr', $membership->souhlas_gdpr))>
                        <span class="ms-2 text-sm text-gray-700">Souhlas GDPR</span>
                    </label>
                    <label for="souhlas_email" class="inline-flex items-center">
                        <input id="souhlas_email" type="checkbox" name="souhlas_email" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('souhlas_email', $membership->souhlas_email))>
                        <span class="ms-2 text-sm text-gray-700">Souhlas se zasíláním e-mailů</span>
                    </label>
                    <label for="souhlas_zverejneni" class="inline-flex items-center">
                        <input id="souhlas_zverejneni" type="checkbox" name="souhlas_zverejneni" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('souhlas_zverejneni', $membership->souhlas_zverejneni))>
                        <span class="ms-2 text-sm text-gray-700">Souhlas se zveřejněním</span>
                    </label>
                </div>
                <div>
                    <x-primary-button>Uložit členství</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
