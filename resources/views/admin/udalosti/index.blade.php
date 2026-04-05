<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="section-eyebrow">Administrace událostí</p>
                <h1 class="text-3xl text-on-surface dark:text-[#e5e2dd]">Přehled vypsaných akcí</h1>
                <p class="max-w-3xl text-sm leading-6 text-on-surface-variant dark:text-[#c3c8bb]">Odtud se vstupuje do detailu akce, správy disciplín, ustájení i pořadatelských reportů.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.dashboard') }}" class="button-secondary">Dashboard</a>
                <a href="{{ route('admin.udalosti.create') }}" class="button-primary">Nová událost</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl">
            <div class="space-y-4">
                @forelse($udalosti as $udalost)
                    <article class="panel p-6">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-3">
                                    <p class="text-xl font-semibold text-on-surface dark:text-[#e5e2dd]">{{ $udalost->nazev }}</p>
                                    <span class="brand-pill">{{ $udalost->aktivni ? 'Aktivní' : 'Archiv' }}</span>
                                </div>
                                <div class="grid gap-2 text-sm text-on-surface-variant dark:text-[#c3c8bb] sm:grid-cols-2">
                                    <p>{{ $udalost->misto }}</p>
                                    <p>{{ $udalost->datum_zacatek?->format('d.m.Y') }} @if($udalost->datum_konec && $udalost->datum_konec->ne($udalost->datum_zacatek))– {{ $udalost->datum_konec->format('d.m.Y') }} @endif</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('admin.udalosti.show', $udalost) }}" class="button-primary">Detail</a>
                                <a href="{{ route('admin.udalosti.edit', $udalost) }}" class="button-secondary">Nastavení</a>
                                <form method="POST" action="{{ route('admin.udalosti.destroy', $udalost) }}" onsubmit="return confirm('Opravdu smazat událost?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-error underline underline-offset-4">Smazat</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="panel p-8 text-sm text-on-surface-variant dark:text-[#c3c8bb]">Zatím nejsou vytvořené žádné události.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
