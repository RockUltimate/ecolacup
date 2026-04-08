<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
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
            <a href="{{ route('admin.kone.index') }}" class="button-secondary">Zpět na koně</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-4xl space-y-6">
            <x-flash-message />

            <div class="panel p-6">
                @include('kone._form', [
                    'kun' => $kun,
                    'formAction' => route('admin.kone.update', $kun),
                    'backHref' => route('admin.kone.index'),
                    'submitLabel' => 'Uložit koně',
                ])
            </div>
        </div>
    </div>
</x-app-layout>
