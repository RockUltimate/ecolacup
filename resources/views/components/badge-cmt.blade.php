@props(['status' => 'none'])

@php
    $map = [
        'active' => ['label' => 'CMT aktivní', 'classes' => 'bg-green-100 text-green-800'],
        'inactive' => ['label' => 'CMT neaktivní', 'classes' => 'bg-amber-100 text-amber-800'],
        'none' => ['label' => 'Bez CMT', 'classes' => 'bg-gray-100 text-gray-700'],
    ];
    $item = $map[$status] ?? $map['none'];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $item['classes'] }}">
    {{ $item['label'] }}
</span>
