@props([
    'udalost',
    'active' => 'overview',
])

@php
    $tabs = [
        'overview' => ['label' => 'Přehled', 'href' => route('admin.udalosti.show', $udalost)],
        'prihlasky' => ['label' => 'Přihlášky', 'href' => route('admin.reports.prihlasky', $udalost)],
        'startky' => ['label' => 'Startky', 'href' => route('admin.reports.startky', $udalost)],
        'ubytovani' => ['label' => 'Ubytování', 'href' => route('admin.reports.ubytovani', $udalost)],
        'settings' => ['label' => 'Nastavení', 'href' => route('admin.udalosti.edit', $udalost)],
    ];
@endphp

<div class="panel p-2">
    <nav class="flex flex-wrap gap-2">
        @foreach($tabs as $key => $tab)
            <a
                href="{{ $tab['href'] }}"
                @class([
                    'px-3 py-2 rounded-md text-sm font-medium transition',
                    'bg-[#3d6b4f] text-white' => $active === $key,
                    'text-[#3d6b4f] hover:bg-emerald-50' => $active !== $key,
                ])
            >
                {{ $tab['label'] }}
            </a>
        @endforeach
    </nav>
</div>
