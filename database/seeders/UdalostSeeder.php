<?php

namespace Database\Seeders;

use App\Models\Udalost;
use Illuminate\Database\Seeder;

class UdalostSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'nazev' => 'OPEN závody Mountain Trail',
                'misto' => 'Haklovy Dvory',
                'datum_zacatek' => '2026-04-04',
                'datum_konec' => '2026-04-04',
                'uzavierka_prihlasek' => '2026-03-31',
                'kapacita' => null,
                'aktivni' => true,
                'popis' => 'Jednodenní OPEN koňské závody s disciplínami ruka/sedlo, tandemem a working race.',
                'moznosti' => [
                    ['nazev' => 'MT Ruka EASY 8+', 'cena' => 300, 'min_vek' => 8],
                    ['nazev' => 'MT Ruka MEDIUM 8+', 'cena' => 300, 'min_vek' => 8],
                    ['nazev' => 'MT Ruka SOLID 8+', 'cena' => 300, 'min_vek' => 8],
                    ['nazev' => 'MT Sedo EASY 8+', 'cena' => 300, 'min_vek' => 8],
                    ['nazev' => 'MT Sedo MEDIUM 8+', 'cena' => 300, 'min_vek' => 8],
                    ['nazev' => 'MT Sedo SOLID 8+', 'cena' => 300, 'min_vek' => 8],
                    ['nazev' => 'MT Ruka – vlastní volba 8+', 'cena' => 250, 'min_vek' => 8],
                    ['nazev' => 'MT Sedo – vlastní volba 8+', 'cena' => 250, 'min_vek' => 8],
                    ['nazev' => 'MT Tandem 12+', 'cena' => 300, 'min_vek' => 12],
                    ['nazev' => 'Working Race ruka 6+ (sezónní speciál)', 'cena' => 300, 'min_vek' => 6],
                    ['nazev' => 'Working Race sedo 6+ (sezónní speciál)', 'cena' => 300, 'min_vek' => 6],
                    ['nazev' => 'Working Trail sedo 8+', 'cena' => 300, 'min_vek' => 8],
                    ['nazev' => 'Trail s vodičem – děti do 8 let', 'cena' => 250, 'min_vek' => 0],
                    ['nazev' => 'Administrativní poplatek', 'cena' => 100, 'je_administrativni_poplatek' => true],
                ],
                'ustajeni' => [
                    ['nazev' => 'Vnitřní boxy 18m2', 'typ' => 'ustajeni', 'cena' => 500, 'kapacita' => 10],
                    ['nazev' => 'Venkovní box 12m2', 'typ' => 'ustajeni', 'cena' => 300, 'kapacita' => 20],
                    ['nazev' => 'Ohrada cca 20x25m (el.ohradník) - pro 2 koně z jedné stáje', 'typ' => 'ustajeni', 'cena' => 400, 'kapacita' => 8],
                    ['nazev' => 'Padock cca 6x8m (el.ohradník)', 'typ' => 'ustajeni', 'cena' => 250, 'kapacita' => 15],
                    ['nazev' => 'Samostatný menší výběh mimo areál', 'typ' => 'ustajeni', 'cena' => 0, 'kapacita' => null],
                    ['nazev' => 'Trénink std.', 'typ' => 'ostatni', 'cena' => 300, 'kapacita' => null],
                ],
            ],
            [
                'nazev' => 'Jarní závody 2026 – Plzeň',
                'misto' => 'Ranč U Koníka, Plzeň',
                'datum_zacatek' => '2026-05-10',
                'datum_konec' => '2026-05-11',
                'uzavierka_prihlasek' => '2026-04-30',
                'kapacita' => 60,
                'aktivni' => true,
                'popis' => 'Dvoudenní jarní koňské závody včetně tandemové disciplíny.',
                'moznosti' => [
                    ['nazev' => 'MT Ruka EASY 8+', 'cena' => 320, 'min_vek' => 8],
                    ['nazev' => 'MT Sedo EASY 8+', 'cena' => 320, 'min_vek' => 8],
                    ['nazev' => 'MT Tandem 12+', 'cena' => 350, 'min_vek' => 12],
                    ['nazev' => 'Working Trail sedo 8+', 'cena' => 320, 'min_vek' => 8],
                    ['nazev' => 'Administrativní poplatek', 'cena' => 100, 'je_administrativni_poplatek' => true],
                ],
                'ustajeni' => [
                    ['nazev' => 'Vnitřní box 16m2', 'typ' => 'ustajeni', 'cena' => 550, 'kapacita' => 20],
                    ['nazev' => 'Venkovní box 12m2', 'typ' => 'ustajeni', 'cena' => 350, 'kapacita' => 30],
                    ['nazev' => 'Padock 6x8m (el.ohradník)', 'typ' => 'ustajeni', 'cena' => 250, 'kapacita' => 20],
                    ['nazev' => 'Trénink std.', 'typ' => 'ostatni', 'cena' => 300, 'kapacita' => null],
                ],
            ],
            [
                'nazev' => 'Letní mistrovství trailu',
                'misto' => 'Farma Borovice, Brno',
                'datum_zacatek' => '2026-07-18',
                'datum_konec' => '2026-07-20',
                'uzavierka_prihlasek' => '2026-07-05',
                'kapacita' => 100,
                'aktivni' => true,
                'popis' => 'Třídenní letní mistrovství se startkami napříč disciplínami.',
                'moznosti' => [
                    ['nazev' => 'MT Ruka SOLID 8+', 'cena' => 380, 'min_vek' => 8],
                    ['nazev' => 'MT Sedo SOLID 8+', 'cena' => 380, 'min_vek' => 8],
                    ['nazev' => 'Working Race sedo 6+', 'cena' => 340, 'min_vek' => 6],
                    ['nazev' => 'MT Tandem 12+', 'cena' => 380, 'min_vek' => 12],
                    ['nazev' => 'Administrativní poplatek', 'cena' => 100, 'je_administrativni_poplatek' => true],
                ],
                'ustajeni' => [
                    ['nazev' => 'Box premium 18m2', 'typ' => 'ustajeni', 'cena' => 650, 'kapacita' => 25],
                    ['nazev' => 'Padock 8x8m (el.ohradník)', 'typ' => 'ustajeni', 'cena' => 300, 'kapacita' => 30],
                    ['nazev' => 'Ubytování jezdec - pokoj', 'typ' => 'ubytovani', 'cena' => 900, 'kapacita' => 20],
                    ['nazev' => 'Trénink std.', 'typ' => 'ostatni', 'cena' => 300, 'kapacita' => null],
                ],
            ],
            [
                'nazev' => 'ARCHIV: OPEN závody Mountain Trail 2025',
                'misto' => 'Haklovy Dvory',
                'datum_zacatek' => '2025-04-06',
                'datum_konec' => '2025-04-06',
                'uzavierka_prihlasek' => '2025-03-28',
                'kapacita' => null,
                'aktivni' => false,
                'popis' => 'Archivní závod pro test zobrazení historie.',
                'moznosti' => [
                    ['nazev' => 'MT Ruka EASY 8+', 'cena' => 280, 'min_vek' => 8],
                    ['nazev' => 'MT Sedo EASY 8+', 'cena' => 280, 'min_vek' => 8],
                    ['nazev' => 'Administrativní poplatek', 'cena' => 100, 'je_administrativni_poplatek' => true],
                ],
                'ustajeni' => [
                    ['nazev' => 'Venkovní box 12m2', 'typ' => 'ustajeni', 'cena' => 300, 'kapacita' => 25],
                    ['nazev' => 'Trénink std.', 'typ' => 'ostatni', 'cena' => 250, 'kapacita' => null],
                ],
            ],
        ];

        foreach ($events as $event) {
            $moznosti = $event['moznosti'] ?? [];
            $ustajeni = $event['ustajeni'] ?? [];
            unset($event['moznosti'], $event['ustajeni']);

            $udalost = Udalost::query()->updateOrCreate(
                ['nazev' => $event['nazev']],
                $event
            );

            $udalost->moznosti()->delete();
            foreach ($moznosti as $index => $moznost) {
                $udalost->moznosti()->create([
                    'nazev' => $moznost['nazev'],
                    'min_vek' => $moznost['min_vek'] ?? null,
                    'cena' => $moznost['cena'],
                    'poradi' => $moznost['poradi'] ?? ($index + 1),
                    'je_administrativni_poplatek' => (bool) ($moznost['je_administrativni_poplatek'] ?? false),
                ]);
            }

            $udalost->ustajeniMoznosti()->delete();
            foreach ($ustajeni as $moznost) {
                $udalost->ustajeniMoznosti()->create([
                    'nazev' => $moznost['nazev'],
                    'typ' => $moznost['typ'],
                    'cena' => $moznost['cena'],
                    'kapacita' => $moznost['kapacita'] ?? null,
                ]);
            }
        }
    }
}
