<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Úprava přihlášky</p>
            <h1 class="text-3xl text-[#20392c]">Přihláška #{{ $prihlaska->id }}</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">Upravujete účastníka, koně, doplňkové položky a poznámku.</p>
        </div>
    </x-slot>
    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-4">
            <div>
                <a href="{{ route('prihlasky.index') }}" class="button-secondary">← Zpět na přihlášky</a>
            </div>
            @include('prihlasky._form', [
                'prihlaska' => $prihlaska,
                'udalost' => $udalost,
                'osoby' => $osoby,
                'kone' => $kone,
            ])
        </div>
    </div>
</x-app-layout>
