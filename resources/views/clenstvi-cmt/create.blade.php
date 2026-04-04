<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nové členství CMT
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="panel p-6">
                @include('clenstvi-cmt._form', ['osoby' => $osoby])
            </div>
        </div>
    </div>
</x-app-layout>
