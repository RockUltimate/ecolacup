<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Nová přihláška</p>
            <h1 class="text-3xl text-on-surface dark:text-[#e5e2dd]">{{ $udalost->nazev }}</h1>
            <p class="max-w-3xl text-sm leading-6 text-on-surface-variant dark:text-[#c3c8bb]">Vyberte osobu, koně a startovní položky. Průběžný součet zůstává viditelný po celou dobu vyplňování.</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl">
            @include('prihlasky._form', ['udalost' => $udalost, 'osoby' => $osoby, 'kone' => $kone])
        </div>
    </div>
</x-app-layout>
