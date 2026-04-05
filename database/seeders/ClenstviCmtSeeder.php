<?php

namespace Database\Seeders;

use App\Models\ClenstviCmt;
use App\Models\Osoba;
use Illuminate\Database\Seeder;
use RuntimeException;

class ClenstviCmtSeeder extends Seeder
{
    public function run(): void
    {
        $clenstvi = [
            [
                'osoba' => ['jmeno' => 'Pavla', 'prijmeni' => 'Cihlová'],
                'typ_clenstvi' => 'fyzicka_osoba',
                'rok' => 2026,
                'cena' => 500,
                'aktivni' => true,
                'evidencni_cislo' => '0002',
                'bydliste' => 'České Budějovice',
                'telefon' => '602012020',
                'email' => 'pavla@example.cz',
                'souhlas_gdpr' => true,
                'souhlas_email' => true,
                'souhlas_zverejneni' => true,
                'sken_prihlaska' => 'clenstvi/pavla-cihlova-2026.jpg',
            ],
            [
                'osoba' => ['jmeno' => 'Lucie', 'prijmeni' => 'Jerhótová'],
                'typ_clenstvi' => 'mladez',
                'rok' => 2026,
                'cena' => 200,
                'aktivni' => true,
                'evidencni_cislo' => '0047',
                'bydliste' => 'Plzeň',
                'telefon' => '777234567',
                'email' => 'lucie@example.cz',
                'zastupce_jmeno' => 'Pavla',
                'zastupce_prijmeni' => 'Cihlová',
                'zastupce_telefon' => '602012020',
                'zastupce_email' => 'pavla@example.cz',
                'souhlas_gdpr' => true,
                'souhlas_email' => true,
                'souhlas_zverejneni' => true,
                'sken_prihlaska' => 'clenstvi/lucie-jerhotova-2026.jpg',
            ],
            [
                'osoba' => ['jmeno' => 'Michael', 'prijmeni' => 'Kabenderle'],
                'typ_clenstvi' => 'fyzicka_osoba',
                'rok' => 2025,
                'cena' => 500,
                'aktivni' => false,
                'evidencni_cislo' => '0103',
                'bydliste' => 'Schachenhausen',
                'telefon' => '728456789',
                'email' => 'michael@example.cz',
                'souhlas_gdpr' => true,
                'souhlas_email' => false,
                'souhlas_zverejneni' => true,
            ],
        ];

        foreach ($clenstvi as $membership) {
            $osoba = Osoba::query()
                ->where('jmeno', $membership['osoba']['jmeno'])
                ->where('prijmeni', $membership['osoba']['prijmeni'])
                ->first();

            if (! $osoba) {
                throw new RuntimeException(
                    'Osoba for ClenstviCmtSeeder not found: '.$membership['osoba']['jmeno'].' '.$membership['osoba']['prijmeni']
                );
            }

            ClenstviCmt::query()->updateOrCreate(
                [
                    'osoba_id' => $osoba->id,
                    'rok' => (int) $membership['rok'],
                ],
                [
                    'organizace_id' => 2,
                    'evidencni_cislo' => $membership['evidencni_cislo'] ?? null,
                    'titul' => $membership['titul'] ?? null,
                    'bydliste' => $membership['bydliste'] ?? null,
                    'telefon' => $membership['telefon'] ?? null,
                    'email' => $membership['email'] ?? null,
                    'nazev_organizace' => $membership['nazev_organizace'] ?? null,
                    'ico' => $membership['ico'] ?? null,
                    'typ_clenstvi' => $membership['typ_clenstvi'],
                    'cena' => $membership['cena'],
                    'aktivni' => (bool) $membership['aktivni'],
                    'zastupce_titul' => $membership['zastupce_titul'] ?? null,
                    'zastupce_jmeno' => $membership['zastupce_jmeno'] ?? null,
                    'zastupce_prijmeni' => $membership['zastupce_prijmeni'] ?? null,
                    'zastupce_rok_narozeni' => $membership['zastupce_rok_narozeni'] ?? null,
                    'zastupce_vztah' => $membership['zastupce_vztah'] ?? null,
                    'zastupce_bydliste' => $membership['zastupce_bydliste'] ?? null,
                    'zastupce_telefon' => $membership['zastupce_telefon'] ?? null,
                    'zastupce_email' => $membership['zastupce_email'] ?? null,
                    'sken_prihlaska' => $membership['sken_prihlaska'] ?? null,
                    'souhlas_gdpr' => (bool) ($membership['souhlas_gdpr'] ?? false),
                    'souhlas_email' => (bool) ($membership['souhlas_email'] ?? false),
                    'souhlas_zverejneni' => (bool) ($membership['souhlas_zverejneni'] ?? false),
                ]
            );
        }
    }
}
