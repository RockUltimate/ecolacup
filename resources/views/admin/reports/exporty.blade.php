<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Exporty</p>
            <h1 class="text-3xl text-[#20392c]">Soubory a výstupy pro pořadatele</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">{{ $udalost->nazev }} • všechny XLS exporty a hromadný PDF balík na jednom místě.</p>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.udalosti.index') }}" class="button-secondary w-full">Zpět na události</a>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6">
            @include('admin.udalosti._tabs', ['udalost' => $udalost, 'active' => 'exporty'])

            <section class="grid gap-5 xl:grid-cols-3">
                <article class="panel p-6 sm:p-8">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="section-eyebrow">Přihlášky</p>
                            <h2 class="mt-2 text-2xl text-[#20392c]">Registrace a kontakty</h2>
                        </div>
                        <span class="brand-pill">XLS + ZIP</span>
                    </div>

                    <p class="mt-4 text-sm leading-6 text-gray-600">Kompletní přehled přihlášených, jejich disciplín, kontaktů a veterinární přejímky.</p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a class="button-secondary" href="{{ route('admin.reports.export.seznam', $udalost) }}">Export seznam</a>
                        <a class="button-secondary" href="{{ route('admin.reports.export.discipliny', $udalost) }}">Export disciplíny</a>
                        <a class="button-secondary" href="{{ route('admin.reports.export.emaily', $udalost) }}">Export e-maily</a>
                        <a class="button-secondary" href="{{ route('admin.reports.export.kone', $udalost) }}">Export vet</a>
                        <a class="button-secondary" href="{{ route('admin.reports.export.bulk-pdf', $udalost) }}">Bulk PDF ZIP</a>
                    </div>
                </article>

                <article class="panel p-6 sm:p-8">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="section-eyebrow">Startky</p>
                            <h2 class="mt-2 text-2xl text-[#20392c]">Startovní listiny</h2>
                        </div>
                        <span class="brand-pill">XLS</span>
                    </div>

                    <p class="mt-4 text-sm leading-6 text-gray-600">Startky podle disciplín a souhrn počtů startů pro další organizaci závodu.</p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('admin.reports.export.startky', $udalost) }}" class="button-secondary">Export startky</a>
                        <a href="{{ route('admin.reports.export.discipliny-pocty', $udalost) }}" class="button-secondary">Export počty</a>
                    </div>
                </article>

                <article class="panel p-6 sm:p-8">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="section-eyebrow">Služby</p>
                            <h2 class="mt-2 text-2xl text-[#20392c]">Ustájení a doplňky</h2>
                        </div>
                        <span class="brand-pill">XLS</span>
                    </div>

                    <p class="mt-4 text-sm leading-6 text-gray-600">Obsazenost ustájení, ubytování, stravy a dalších položek spojených s událostí.</p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('admin.reports.export.ubytovani', $udalost) }}" class="button-secondary">Export ustájení a ubytování</a>
                    </div>
                </article>
            </section>
        </div>
    </div>
</x-app-layout>
