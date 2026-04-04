<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nová přihláška • {{ $udalost->nazev }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow sm:rounded-lg">
                @include('prihlasky._form', ['udalost' => $udalost, 'osoby' => $osoby, 'kone' => $kone])
            </div>
        </div>
    </div>
</x-app-layout>
