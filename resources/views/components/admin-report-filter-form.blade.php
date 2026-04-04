@props([
    'action',
    'resetHref',
    'formClass' => 'grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_180px_auto] gap-3 items-end',
])

<div class="panel p-4">
    <form method="GET" action="{{ $action }}" class="{{ $formClass }}">
        {{ $slot }}
        <div class="flex items-center gap-2">
            <x-primary-button>Filtrovat</x-primary-button>
            <a href="{{ $resetHref }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Reset</a>
        </div>
    </form>
</div>
