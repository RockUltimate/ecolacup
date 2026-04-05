<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-on-surface dark:text-[#e5e2dd] leading-tight">
            Nový kůň
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="panel p-6">
                @include('kone._form', ['plemena' => $plemena])
            </div>
        </div>
    </div>
</x-app-layout>
