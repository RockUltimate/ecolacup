<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <div class="space-y-3">
                <p class="section-eyebrow">Administrace událostí</p>
                <h1 class="text-3xl text-[#20392c]">Přehled vypsaných akcí</h1>
                <p class="max-w-3xl text-sm leading-6 text-gray-600">Odtud se vstupuje do detailu akce, správy disciplín, ustájení i pořadatelských reportů.</p>
            </div>
            <div class="flex justify-start">
                <a href="{{ route('admin.udalosti.create') }}" class="button-primary">Nová událost</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl">
            <div class="space-y-4">
                @forelse($udalosti as $udalost)
                    <article class="panel overflow-hidden">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-stretch">
                            <a href="{{ route('admin.udalosti.edit', $udalost) }}" class="block flex-1 p-6 transition duration-200 hover:bg-white/70 hover:shadow-[0_26px_80px_rgba(71,52,34,0.08)]">
                                <div class="flex h-full flex-col justify-center space-y-3">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <p class="text-xl font-semibold text-[#20392c]">{{ $udalost->nazev }}</p>
                                        <span class="brand-pill">{{ $udalost->aktivni ? 'Aktivní' : 'Archiv' }}</span>
                                    </div>
                                    <div class="grid gap-2 text-sm text-gray-600 sm:grid-cols-2">
                                        <p>{{ $udalost->misto }}</p>
                                        <p>{{ $udalost->datum_zacatek?->format('d.m.Y') }} @if($udalost->datum_konec && $udalost->datum_konec->ne($udalost->datum_zacatek))– {{ $udalost->datum_konec->format('d.m.Y') }} @endif</p>
                                    </div>
                                </div>
                            </a>

                            <div class="flex w-[170px] max-w-full items-center px-6 pb-6 lg:px-6 lg:pb-0">
                                <form method="POST" action="{{ route('admin.udalosti.destroy', $udalost) }}" class="w-full" onsubmit="return confirm('Opravdu smazat událost?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button-secondary w-full border-red-200 bg-red-50 text-red-700 hover:bg-red-100">Smazat</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="panel p-8 text-sm text-gray-600">Zatím nejsou vytvořené žádné události.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
