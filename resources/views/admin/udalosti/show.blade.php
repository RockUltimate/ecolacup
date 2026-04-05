<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin • {{ $udalost->nazev }}</h2>
            <a href="{{ route('admin.udalosti.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Zpět na události</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('admin.udalosti._tabs', ['udalost' => $udalost, 'active' => 'overview'])

            <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <article class="panel p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Aktivní přihlášky</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($udalost->active_prihlasky_count) }}</p>
                </article>
                <article class="panel p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Smazané přihlášky</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($udalost->deleted_prihlasky_count) }}</p>
                </article>
                <article class="panel p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Disciplíny</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($udalost->moznosti_count) }}</p>
                </article>
                <article class="panel p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Ustájení/Ubytování</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($udalost->ustajeni_moznosti_count) }}</p>
                </article>
            </section>

            <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <article class="panel p-5 xl:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900">Poslední přihlášky</h3>
                    <div class="mt-4 space-y-3">
                        @forelse($recentRegistrations as $registration)
                            <div class="rounded-lg border border-gray-200 p-3">
                                <p class="font-medium text-gray-900">#{{ $registration->start_cislo ?? '—' }} • {{ $registration->osoba?->prijmeni }} {{ $registration->osoba?->jmeno }}</p>
                                <p class="text-sm text-gray-600">{{ $registration->kun?->jmeno }} • {{ number_format((float) $registration->cena_celkem, 2, ',', ' ') }} Kč</p>
                                <p class="text-xs text-gray-500">{{ $registration->created_at?->format('d.m.Y H:i') }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-600">Zatím nejsou žádné aktivní přihlášky.</p>
                        @endforelse
                    </div>
                </article>

                <aside class="panel p-5 space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900">Událost</h3>
                    <p class="text-sm text-gray-700">{{ $udalost->misto }}</p>
                    <p class="text-sm text-gray-700">Termín: {{ $udalost->datum_zacatek?->format('d.m.Y') }} @if($udalost->datum_konec && $udalost->datum_konec->ne($udalost->datum_zacatek))– {{ $udalost->datum_konec->format('d.m.Y') }}@endif</p>
                    <p class="text-sm text-gray-700">Uzávěrka přihlášek: {{ $udalost->uzavierka_prihlasek?->format('d.m.Y') }}</p>
                    <p class="text-sm text-gray-700">Kapacita: {{ $udalost->kapacita ? number_format($udalost->kapacita) : 'Neomezená' }}</p>
                    <a href="{{ route('admin.start-cisla.show', $udalost) }}" class="inline-flex text-sm text-indigo-600 hover:text-indigo-800 underline">Správa startovních čísel</a>
                </aside>
            </section>
        </div>
    </div>
</x-app-layout>
