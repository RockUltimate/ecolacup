<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <p class="section-eyebrow">Admin • Kůň</p>
            <h1 class="text-3xl text-[#20392c]">Upravit koně</h1>
            <p class="max-w-3xl text-sm leading-6 text-gray-600">
                @if($kun->user)
                    Vlastník: <a href="{{ route('admin.users.edit', $kun->user) }}" class="text-[#3d6b4f] underline underline-offset-4">{{ $kun->user->celeJmeno() }}</a> • {{ $kun->user->email }}
                @else
                    Vlastník: nepřiřazen
                @endif
            </p>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.kone.index') }}" class="button-secondary w-full">Zpět na koně</a>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl space-y-6">
            <x-flash-message />

            <div class="panel p-6">
                @include('kone._form', [
                    'kun' => $kun,
                    'formId' => 'admin-kun-edit-form',
                    'formAction' => route('admin.kone.update', $kun),
                    'backHref' => route('admin.kone.index'),
                    'submitLabel' => 'Uložit koně',
                    'showFooterActions' => false,
                ])

                <div class="mt-6 flex w-[170px] max-w-full flex-col gap-3">
                    <button type="submit" form="admin-kun-edit-form" class="button-primary w-full">Uložit koně</button>

                    <form method="POST" action="{{ route('admin.kone.destroy', $kun) }}" class="w-full" onsubmit="return confirm('Opravdu smazat koně?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button-secondary w-full border-red-200 bg-red-50 text-red-700 hover:bg-red-100">
                            Smazat koně
                        </button>
                    </form>

                    <a href="{{ route('admin.kone.index') }}" class="text-sm text-gray-600 underline hover:text-gray-900">Zpět na přehled</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
