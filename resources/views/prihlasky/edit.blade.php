<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Úprava přihlášky</p>
            <h1 class="text-3xl text-on-surface dark:text-[#e5e2dd]">Přihláška #{{ $prihlaska->id }}</h1>
            <p class="max-w-3xl text-sm leading-6 text-on-surface-variant dark:text-[#c3c8bb]">Upravujete doplňkové položky, poznámku a souhrn. Účastník i hlavní kůň zůstávají po vytvoření uzamčené.</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl">
            @include('prihlasky._form', [
                'prihlaska' => $prihlaska,
                'udalost' => $udalost,
                'osoby' => $osoby,
                'kone' => $kone,
            ])
        </div>
    </div>
</x-app-layout>
