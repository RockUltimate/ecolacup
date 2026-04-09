<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Nová událost</p>
            <h1 class="text-3xl text-[#20392c]">Založit novou událost</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">Vyplňte základní informace, termín, materiály a popis akce.</p>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.udalosti.index') }}" class="button-secondary w-full">Zpět na události</a>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6">
            @include('admin.udalosti._popis')
        </div>
    </div>
</x-app-layout>
