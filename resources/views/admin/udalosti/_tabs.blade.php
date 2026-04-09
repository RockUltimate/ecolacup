@props([
    'udalost',
    'active' => 'popis',
])

@php
    $tabs = [
        'popis' => ['label' => 'Popis', 'href' => route('admin.udalosti.edit', $udalost) . '#popis'],
        'discipliny' => ['label' => 'Disciplíny', 'href' => route('admin.udalosti.edit', $udalost) . '#discipliny'],
        'sluzby' => ['label' => 'Služby', 'href' => route('admin.udalosti.edit', $udalost) . '#sluzby'],
        'prihlasky' => ['label' => 'Přihlášky', 'href' => route('admin.reports.prihlasky', $udalost)],
        'startky' => ['label' => 'Startky', 'href' => route('admin.reports.startky', $udalost)],
        'exporty' => ['label' => 'Exporty', 'href' => route('admin.reports.exporty', $udalost)],
    ];
@endphp

<div class="admin-tab-strip">
    <nav class="admin-tab-nav">
        @foreach($tabs as $key => $tab)
            <a
                href="{{ $tab['href'] }}"
                @class([
                    'admin-tab',
                    'admin-tab--active' => $active === $key,
                    'admin-tab--inactive' => $active !== $key,
                ])
            >
                {{ $tab['label'] }}
            </a>
        @endforeach
    </nav>
</div>
