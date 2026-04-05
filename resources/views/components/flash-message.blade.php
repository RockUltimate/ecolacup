@php
    $status = session('status');
    $messages = [
        'osoba-created' => 'Osoba byla vytvořena.',
        'osoba-updated' => 'Osoba byla upravena.',
        'osoba-deleted' => 'Osoba byla smazána.',
        'clenstvi-created' => 'Členství bylo vytvořeno.',
        'clenstvi-updated' => 'Členství bylo upraveno.',
        'clenstvi-deleted' => 'Členství bylo smazáno.',
        'clenstvi-renewed' => 'Členství bylo prodlouženo na další rok.',
        'clenstvi-renew-exists' => 'Členství pro další rok už existuje.',
        'start-cislo-updated' => 'Startovní číslo bylo uloženo.',
        'start-cisla-normalized' => 'Startovní čísla byla srovnána.',
        'prihlaska-created' => 'Přihláška byla vytvořena.',
        'prihlaska-updated' => 'Přihláška byla upravena.',
        'prihlaska-deleted' => 'Přihláška byla smazána.',
        'kun-created' => 'Kůň byl vytvořen.',
        'kun-updated' => 'Kůň byl upraven.',
        'kun-deleted' => 'Kůň byl smazán.',
        'udalost-created' => 'Událost byla vytvořena.',
        'udalost-updated' => 'Událost byla upravena.',
        'udalost-deleted' => 'Událost byla smazána.',
        'admin-user-updated' => 'Uživatel byl upraven.',
        'admin-clenstvi-updated' => 'Členství bylo upraveno v administraci.',
    ];
@endphp

@if ($status)
    <div class="mb-4 panel p-3 text-sm text-[#3d6b4f]">
        {{ $messages[$status] ?? $status }}
    </div>
@endif
