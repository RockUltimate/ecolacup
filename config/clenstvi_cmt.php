<?php

return [
    'new_member_admin_fee' => 100,
    'membership_types' => [
        'fyzicka_osoba' => [
            'label' => 'Fyzická osoba',
            'default_price' => 500,
        ],
        'mladez' => [
            'label' => 'Mládež',
            'default_price' => 200,
        ],
        'pravnicka_osoba' => [
            'label' => 'Právnická osoba',
            'default_price' => 800,
        ],
    ],
    'yearly_prices' => [
        2026 => [
            'fyzicka_osoba' => 500,
            'mladez' => 200,
            'pravnicka_osoba' => 800,
        ],
        2027 => [
            'fyzicka_osoba' => 500,
            'mladez' => 200,
            'pravnicka_osoba' => 800,
        ],
    ],
];
