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

<div class="panel p-3">
    <nav class="flex flex-wrap gap-2">
        @foreach($tabs as $key => $tab)
            <a
                href="{{ $tab['href'] }}"
                @class([
                    'rounded-full border px-4 py-2 text-sm font-semibold transition',
                    'border-[#20392c] bg-[#20392c] text-white' => $active === $key,
                    'border-[#ddd0bc] bg-white/70 text-[#3d6b4f] hover:bg-emerald-50' => $active !== $key,
                ])
            >
                {{ $tab['label'] }}
            </a>
        @endforeach
    </nav>
</div>
