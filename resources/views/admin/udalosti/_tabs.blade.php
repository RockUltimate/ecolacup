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
                    'border-primary bg-primary text-white dark:border-inverse-primary dark:bg-primary-container dark:text-on-primary-container' => $active === $key,
                    'border-outline-variant/40 bg-surface-container-lowest/70 text-primary hover:bg-surface-container-low dark:border-[#43493e]/40 dark:bg-[#2a2a27]/70 dark:text-inverse-primary dark:hover:bg-[#2a2a27]' => $active !== $key,
                ])
            >
                {{ $tab['label'] }}
            </a>
        @endforeach
    </nav>
</div>
