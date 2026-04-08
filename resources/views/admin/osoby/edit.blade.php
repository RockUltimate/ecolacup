<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="section-eyebrow">Admin • Osoba</p>
                <h1 class="text-3xl text-[#20392c]">Upravit osobu</h1>
                <p class="max-w-3xl text-sm leading-6 text-gray-600">
                    @if($osoba->user)
                        Uživatel: <a href="{{ route('admin.users.edit', $osoba->user) }}" class="text-[#3d6b4f] underline underline-offset-4">{{ $osoba->user->celeJmeno() }}</a> • {{ $osoba->user->email }}
                    @else
                        Uživatel: nepřiřazen
                    @endif
                </p>
            </div>
            <a href="{{ route('admin.osoby.index') }}" class="button-secondary">Zpět na osoby</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-3xl space-y-6">
            <x-flash-message />

            <div class="panel p-6">
                @include('osoby._form', [
                    'osoba' => $osoba,
                    'formAction' => route('admin.osoby.update', $osoba),
                    'backHref' => route('admin.osoby.index'),
                    'submitLabel' => 'Uložit osobu',
                ])
            </div>
        </div>
    </div>
</x-app-layout>
