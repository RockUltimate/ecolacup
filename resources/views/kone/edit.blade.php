<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Koně</p>
            <h1 class="text-3xl text-[#20392c]">Upravit koně</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">Aktualizujte údaje o koni, jeho průkazu a vlastníkovi před další registrací.</p>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('kone.index') }}" class="button-secondary w-full">Zpět na koně</a>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl">
            <div class="panel p-6">
                @include('kone._form', ['kun' => $kun])
            </div>
        </div>
    </div>
</x-app-layout>
