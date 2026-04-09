<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Moji koně</p>
            <h1 class="text-3xl text-[#20392c]">Přehled koní a jejich údajů</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">Spravujte koně, jejich průkazy a připravenost pro přihlášky na události.</p>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('kone.create') }}" class="button-primary w-full">Nový kůň</a>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl">
            <x-flash-message />

            <div class="panel overflow-hidden">
                <div class="divide-y divide-[#eadfcc]">
                    @forelse ($kone as $kun)
                        @php($passportComplete = filled($kun->cislo_prukazu) && filled($kun->cislo_hospodarstvi) && filled($kun->majitel_jmeno_adresa))
                        <div class="p-5">
                            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                <div class="space-y-2">
                                    <h3 class="text-lg font-semibold text-[#20392c]">{{ $kun->jmeno }}</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $kun->plemeno_nazev ?: $kun->plemeno_vlastni ?: $kun->plemeno_kod ?: 'Bez plemene' }} • {{ $kun->rok_narozeni }} • {{ $kun->staj }}
                                    </p>
                                    <div class="mt-2">
                                        <span @class([
                                            'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                            'bg-emerald-100 text-emerald-700' => $passportComplete,
                                            'bg-amber-100 text-amber-700' => ! $passportComplete,
                                        ])>
                                            {{ $passportComplete ? 'Průkaz kompletní' : 'Průkaz nekompletní' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex w-[170px] max-w-full flex-col gap-3">
                                    <a href="{{ route('kone.edit', $kun) }}" class="button-secondary w-full">Upravit</a>
                                    <form method="POST" action="{{ route('kone.destroy', $kun) }}" class="w-full" onsubmit="return confirm('Opravdu smazat koně?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button-secondary w-full border-red-200 bg-red-50 text-red-700 hover:bg-red-100">Smazat</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-sm text-gray-600">
                            Zatím nemáte žádné koně. Přidejte první záznam.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
