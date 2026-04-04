@php
    $status = session('status');
    $messages = [
        'osoba-created' => 'Osoba byla vytvořena.',
        'osoba-updated' => 'Osoba byla upravena.',
        'osoba-deleted' => 'Osoba byla smazána.',
        'clenstvi-created' => 'Členství bylo vytvořeno.',
        'clenstvi-updated' => 'Členství bylo upraveno.',
        'clenstvi-deleted' => 'Členství bylo smazáno.',
        'prihlaska-created' => 'Přihláška byla vytvořena.',
        'prihlaska-updated' => 'Přihláška byla upravena.',
        'prihlaska-deleted' => 'Přihláška byla smazána.',
        'udalost-created' => 'Událost byla vytvořena.',
        'udalost-updated' => 'Událost byla upravena.',
        'udalost-deleted' => 'Událost byla smazána.',
    ];
@endphp

@if ($status)
    <div class="mb-4 panel p-3 text-sm text-[#3d6b4f]">
        {{ $messages[$status] ?? $status }}
    </div>
@endif
