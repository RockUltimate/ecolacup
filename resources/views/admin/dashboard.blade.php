<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin • Dashboard</h2>
            <a href="{{ route('admin.udalosti.index') }}" class="inline-flex items-center px-4 py-2 border border-[#3d6b4f] rounded-md font-semibold text-xs uppercase tracking-widest text-[#3d6b4f] hover:bg-emerald-50">
                Správa událostí
            </a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <article class="panel p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Uživatelé</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($stats['users']) }}</p>
                </article>
                <article class="panel p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Koně</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($stats['horses']) }}</p>
                </article>
                <article class="panel p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Události</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($stats['events']) }}</p>
                </article>
                <article class="panel p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Aktivní přihlášky</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($stats['registrations']) }}</p>
                </article>
            </section>

            <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <article class="panel p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Nadcházející události</h3>
                        <a href="{{ route('admin.udalosti.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Všechny události</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($upcomingEvents as $event)
                            <div class="rounded-lg border border-gray-200 p-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $event->nazev }}</p>
                                        <p class="text-sm text-gray-600">{{ $event->misto }}</p>
                                        <p class="text-xs text-gray-500">{{ $event->datum_zacatek?->format('d.m.Y') }} @if($event->datum_konec && $event->datum_konec->ne($event->datum_zacatek)) – {{ $event->datum_konec->format('d.m.Y') }} @endif</p>
                                    </div>
                                    <span class="brand-pill">{{ $event->active_registrations_count }} přihlášek</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-600">Žádné nadcházející události.</p>
                        @endforelse
                    </div>
                </article>

                <article class="panel p-5">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Poslední přihlášky</h3>
                    <div class="space-y-3">
                        @forelse($recentRegistrations as $registration)
                            <div class="rounded-lg border border-gray-200 p-3">
                                <p class="font-medium text-gray-900">#{{ $registration->start_cislo ?? '—' }} • {{ $registration->osoba?->prijmeni }} {{ $registration->osoba?->jmeno }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $registration->udalost?->nazev }} • {{ $registration->kun?->jmeno }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $registration->created_at?->format('d.m.Y H:i') }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-600">Zatím nejsou žádné přihlášky.</p>
                        @endforelse
                    </div>
                </article>
            </section>
        </div>
    </div>
</x-app-layout>
