<x-app-layout>
    <x-slot name="header">
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
    </x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.osoby.index') }}" class="button-secondary w-full">Zpět na osoby</a>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl space-y-6">
            <x-flash-message />

            <div class="panel p-6">
                @include('osoby._form', [
                    'osoba' => $osoba,
                    'formId' => 'admin-osoba-edit-form',
                    'formAction' => route('admin.osoby.update', $osoba),
                    'backHref' => route('admin.osoby.index'),
                    'submitLabel' => 'Uložit osobu',
                    'showFooterActions' => false,
                ])

                <div class="mt-6 flex w-[170px] max-w-full flex-col gap-3">
                    <button type="submit" form="admin-osoba-edit-form" class="button-primary w-full">Uložit osobu</button>

                    <form method="POST" action="{{ route('admin.osoby.destroy', $osoba) }}" class="w-full" onsubmit="return confirm('Opravdu smazat osobu?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button-secondary w-full border-red-200 bg-red-50 text-red-700 hover:bg-red-100">
                            Smazat osobu
                        </button>
                    </form>

                    <a href="{{ route('admin.osoby.index') }}" class="text-sm text-gray-600 underline hover:text-gray-900">Zpět na přehled</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
