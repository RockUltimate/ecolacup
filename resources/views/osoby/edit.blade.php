<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Osoby</p>
            <h1 class="text-3xl text-[#20392c]">Upravit osobu</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">Aktualizujte údaje účastníka, které se používají v přihláškách na události.</p>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('osoby.index') }}" class="button-secondary w-full">Zpět na osoby</a>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl">
            <div class="panel p-6">
                @include('osoby._form', ['osoba' => $osoba])
            </div>
        </div>
    </div>
</x-app-layout>
