<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="section-eyebrow">Administrace</p>
                <h1 class="text-3xl text-[#20392c]">Provozní přehled platformy</h1>
                <p class="max-w-3xl text-sm leading-6 text-gray-600">Rychlý souhrn objemu registrací, nadcházejících akcí a posledních změn, které potřebují pozornost pořadatele.</p>
            </div>
            <a href="{{ route('admin.udalosti.index') }}" class="button-primary">Správa událostí</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-8">
            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="panel p-6">
                    <p class="section-eyebrow">Uživatelé</p>
                    <p class="mt-3 text-4xl font-semibold text-[#20392c]">{{ number_format($stats['users']) }}</p>
                </article>
                <article class="panel p-6">
                    <p class="section-eyebrow">Koně</p>
                    <p class="mt-3 text-4xl font-semibold text-[#20392c]">{{ number_format($stats['horses']) }}</p>
                </article>
                <article class="panel p-6">
                    <p class="section-eyebrow">Události</p>
                    <p class="mt-3 text-4xl font-semibold text-[#20392c]">{{ number_format($stats['events']) }}</p>
                </article>
                <article class="panel p-6">
                    <p class="section-eyebrow">Aktivní přihlášky</p>
                    <p class="mt-3 text-4xl font-semibold text-[#20392c]">{{ number_format($stats['registrations']) }}</p>
                </article>
            </section>

            <section class="panel p-5">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.users.index') }}" class="button-secondary">Správa uživatelů</a>
                    <a href="{{ route('admin.clenstvi.index') }}" class="button-secondary">Správa členství CMT</a>
                    <a href="{{ route('admin.udalosti.index') }}" class="button-secondary">Události</a>
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)]">
                <article class="panel p-6 sm:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="section-eyebrow">Kalendář</p>
                            <h2 class="mt-2 text-2xl text-[#20392c]">Nadcházející události</h2>
                        </div>
                        <a href="{{ route('admin.udalosti.index') }}" class="text-sm text-[#7b5230] underline underline-offset-4">Všechny události</a>
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse($upcomingEvents as $event)
                            <a href="{{ route('admin.udalosti.show', $event) }}" class="surface-muted block transition hover:bg-white/80">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-lg font-semibold text-[#20392c]">{{ $event->nazev }}</p>
                                        <p class="mt-1 text-sm text-gray-600">{{ $event->misto }}</p>
                                        <p class="mt-1 text-sm text-gray-500">{{ $event->datum_zacatek?->format('d.m.Y') }} @if($event->datum_konec && $event->datum_konec->ne($event->datum_zacatek))– {{ $event->datum_konec->format('d.m.Y') }} @endif</p>
                                    </div>
                                    <span class="brand-pill">{{ $event->active_registrations_count }} přihlášek</span>
                                </div>
                            </a>
                        @empty
                            <p class="text-sm text-gray-600">Žádné nadcházející události.</p>
                        @endforelse
                    </div>
                </article>

                <article class="panel p-6 sm:p-8">
                    <p class="section-eyebrow">Aktivita</p>
                    <h2 class="mt-2 text-2xl text-[#20392c]">Poslední přihlášky</h2>

                    <div class="mt-6 space-y-4">
                        @forelse($recentRegistrations as $registration)
                            <div class="surface-muted">
                                <p class="font-semibold text-[#20392c]">#{{ $registration->start_cislo ?? '—' }} • {{ $registration->osoba?->prijmeni }} {{ $registration->osoba?->jmeno }}</p>
                                <p class="mt-1 text-sm text-gray-600">{{ $registration->udalost?->nazev }} • {{ $registration->kun?->jmeno }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $registration->created_at?->format('d.m.Y H:i') }}</p>
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
