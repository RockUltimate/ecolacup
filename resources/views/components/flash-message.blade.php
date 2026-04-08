@php
    $status = session('status');
    $messages = [
        'osoba-created' => 'Osoba byla vytvořena.',
        'osoba-updated' => 'Osoba byla upravena.',
        'osoba-deleted' => 'Osoba byla smazána.',
        'start-cislo-updated' => 'Startovní číslo bylo uloženo.',
        'start-cisla-normalized' => 'Startovní čísla byla srovnána.',
        'homepage-message-updated' => 'Text hlavní novinky byl upraven.',
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
        'admin-user-purged' => 'Uživatel byl trvale odstraněn (GDPR purge).',
        'admin-user-purge-self-denied' => 'Nelze odstranit právě přihlášeného administrátora.',
        'admin-kun-duplicates-synced' => 'Vybraný popis koně byl zkopírován do všech stejných instancí.',
    ];
@endphp

@if ($status)
    <div class="mb-4 panel p-3 text-sm text-[#3d6b4f]">
        {{ $messages[$status] ?? $status }}
    </div>
@endif
