<?php

namespace Database\Seeders;

use App\Models\Kun;
use App\Models\Osoba;
use App\Models\Prihlaska;
use App\Models\Udalost;
use App\Services\PrihlaskaService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PrihlaskaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('prihlasky_ustajeni')->delete();
        DB::table('prihlasky_polozky')->delete();
        DB::table('prihlasky')->delete();

        /** @var PrihlaskaService $prihlaskaService */
        $prihlaskaService = app(PrihlaskaService::class);

        $registrations = [
            [
                'udalost' => 'OPEN závody Mountain Trail',
                'osoba' => ['jmeno' => 'Pavla', 'prijmeni' => 'Cihlová'],
                'kun' => 'Chance',
                'moznosti' => ['MT Ruka EASY 8+', 'Administrativní poplatek'],
                'ustajeni' => ['Vnitřní boxy 18m2'],
                'poznamka' => 'Prosím o ranní start.',
            ],
            [
                'udalost' => 'OPEN závody Mountain Trail',
                'osoba' => ['jmeno' => 'Kristýna', 'prijmeni' => 'Kubů'],
                'kun' => 'Hugo Lucky Star',
                'moznosti' => ['MT Sedo EASY 8+', 'Administrativní poplatek'],
                'ustajeni' => ['Padock cca 6x8m (el.ohradník)'],
            ],
            [
                'udalost' => 'OPEN závody Mountain Trail',
                'osoba' => ['jmeno' => 'Paulína', 'prijmeni' => 'Jindřichová'],
                'kun' => 'On Star',
                'moznosti' => ['Working Trail sedo 8+', 'Administrativní poplatek'],
                'ustajeni' => ['Venkovní box 12m2'],
            ],
            [
                'udalost' => 'OPEN závody Mountain Trail',
                'osoba' => ['jmeno' => 'Lucie', 'prijmeni' => 'Jerhótová'],
                'kun' => 'Joseph Small',
                'moznosti' => ['MT Ruka MEDIUM 8+', 'Administrativní poplatek'],
                'poznamka' => 'Příjezd v pátek večer.',
            ],
            [
                'udalost' => 'OPEN závody Mountain Trail',
                'osoba' => ['jmeno' => 'Melanie', 'prijmeni' => 'Kirchner'],
                'kun' => 'Joseph Small',
                'moznosti' => ['MT Sedo MEDIUM 8+', 'Administrativní poplatek'],
                'ustajeni' => ['Trénink std.'],
            ],
            [
                'udalost' => 'OPEN závody Mountain Trail',
                'osoba' => ['jmeno' => 'Markéta', 'prijmeni' => 'Plekánková'],
                'kun' => 'Accademia\'s Midnight Expresso',
                'moznosti' => ['MT Sedo SOLID 8+', 'Administrativní poplatek'],
                'ustajeni' => ['Venkovní box 12m2'],
            ],
            [
                'udalost' => 'OPEN závody Mountain Trail',
                'osoba' => ['jmeno' => 'Michael', 'prijmeni' => 'Kabenderle'],
                'kun' => 'Atlantis Viking',
                'kun_tandem' => 'Cuckoo Moonshine',
                'moznosti' => ['MT Tandem 12+', 'Administrativní poplatek'],
                'ustajeni' => ['Ohrada cca 20x25m (el.ohradník) - pro 2 koně z jedné stáje'],
            ],
            [
                'udalost' => 'OPEN závody Mountain Trail',
                'osoba' => ['jmeno' => 'Simon', 'prijmeni' => 'Meier'],
                'kun' => 'Cuckoo Moonshine',
                'moznosti' => ['Working Race ruka 6+ (sezónní speciál)', 'Administrativní poplatek'],
            ],
            [
                'udalost' => 'OPEN závody Mountain Trail',
                'osoba' => ['jmeno' => 'Gabriela', 'prijmeni' => 'Plekánková'],
                'kun' => 'Accademia\'s Midnight Expresso',
                'moznosti' => ['MT Ruka – vlastní volba 8+', 'Administrativní poplatek'],
                'smazana' => true,
            ],
            [
                'udalost' => 'CMT Jaro 2026 – Plzeň',
                'osoba' => ['jmeno' => 'Pavla', 'prijmeni' => 'Cihlová'],
                'kun' => 'Escape Donna',
                'moznosti' => ['MT Ruka EASY 8+', 'Administrativní poplatek'],
                'ustajeni' => ['Vnitřní box 16m2'],
            ],
            [
                'udalost' => 'CMT Jaro 2026 – Plzeň',
                'osoba' => ['jmeno' => 'Lucie', 'prijmeni' => 'Jerhótová'],
                'kun' => 'Joseph Small',
                'moznosti' => ['MT Sedo EASY 8+', 'Administrativní poplatek'],
                'ustajeni' => ['Padock 6x8m (el.ohradník)'],
            ],
            [
                'udalost' => 'CMT Jaro 2026 – Plzeň',
                'osoba' => ['jmeno' => 'Michael', 'prijmeni' => 'Kabenderle'],
                'kun' => 'Atlantis Viking',
                'kun_tandem' => 'Cuckoo Moonshine',
                'moznosti' => ['MT Tandem 12+', 'Administrativní poplatek'],
            ],
            [
                'udalost' => 'Letní mistrovství CMT',
                'osoba' => ['jmeno' => 'Pavla', 'prijmeni' => 'Cihlová'],
                'kun' => 'Chance',
                'moznosti' => ['MT Ruka SOLID 8+', 'Administrativní poplatek'],
                'ustajeni' => ['Box premium 18m2'],
            ],
            [
                'udalost' => 'Letní mistrovství CMT',
                'osoba' => ['jmeno' => 'Simon', 'prijmeni' => 'Meier'],
                'kun' => 'Cuckoo Moonshine',
                'moznosti' => ['Working Race sedo 6+', 'Administrativní poplatek'],
                'ustajeni' => ['Padock 8x8m (el.ohradník)'],
            ],
            [
                'udalost' => 'Letní mistrovství CMT',
                'osoba' => ['jmeno' => 'Markéta', 'prijmeni' => 'Plekánková'],
                'kun' => 'Accademia\'s Midnight Expresso',
                'moznosti' => ['MT Sedo SOLID 8+', 'Administrativní poplatek'],
                'ustajeni' => ['Ubytování jezdec - pokoj'],
            ],
        ];

        foreach ($registrations as $seed) {
            $udalost = Udalost::query()->where('nazev', $seed['udalost'])->first();
            if (! $udalost) {
                throw new RuntimeException('Udalost not found for PrihlaskaSeeder: '.$seed['udalost']);
            }

            $osoba = Osoba::query()
                ->where('jmeno', $seed['osoba']['jmeno'])
                ->where('prijmeni', $seed['osoba']['prijmeni'])
                ->first();
            if (! $osoba) {
                throw new RuntimeException(
                    'Osoba not found for PrihlaskaSeeder: '.$seed['osoba']['jmeno'].' '.$seed['osoba']['prijmeni']
                );
            }

            $kun = Kun::query()
                ->where('user_id', $osoba->user_id)
                ->where('jmeno', $seed['kun'])
                ->first();
            if (! $kun) {
                throw new RuntimeException('Kun not found for PrihlaskaSeeder: '.$seed['kun']);
            }

            $kunTandem = null;
            if (! empty($seed['kun_tandem'])) {
                $kunTandem = Kun::query()
                    ->where('user_id', $osoba->user_id)
                    ->where('jmeno', $seed['kun_tandem'])
                    ->first();
                if (! $kunTandem) {
                    throw new RuntimeException('Tandem kun not found for PrihlaskaSeeder: '.$seed['kun_tandem']);
                }
            }

            $prihlaska = Prihlaska::query()->create([
                'udalost_id' => $udalost->id,
                'user_id' => $osoba->user_id,
                'osoba_id' => $osoba->id,
                'kun_id' => $kun->id,
                'kun_tandem_id' => $kunTandem?->id,
                'start_cislo' => null,
                'poznamka' => $seed['poznamka'] ?? null,
                'gdpr_souhlas' => true,
                'cena_celkem' => 0,
                'smazana' => false,
            ]);

            $moznostIds = $this->resolveMoznostIds($udalost, $seed['moznosti']);
            $ustajeniIds = $this->resolveUstajeniIds($udalost, $seed['ustajeni'] ?? []);

            $prihlaskaService->syncPrihlaska($prihlaska, $moznostIds, $ustajeniIds);

            if (! empty($seed['smazana'])) {
                $prihlaska->update(['smazana' => true]);
                $prihlaska->delete();
            }
        }
    }

    /**
     * @param  array<int, string>  $names
     * @return array<int, int>
     */
    private function resolveMoznostIds(Udalost $udalost, array $names): array
    {
        if ($names === []) {
            return [];
        }

        $moznosti = $udalost->moznosti()
            ->whereIn('nazev', $names)
            ->get()
            ->keyBy('nazev');

        $missing = array_values(array_diff($names, $moznosti->keys()->all()));
        if ($missing !== []) {
            throw new RuntimeException('Missing moznosti for '.$udalost->nazev.': '.implode(', ', $missing));
        }

        return array_map(
            static fn (string $name): int => (int) $moznosti[$name]->id,
            $names
        );
    }

    /**
     * @param  array<int, string>  $names
     * @return array<int, int>
     */
    private function resolveUstajeniIds(Udalost $udalost, array $names): array
    {
        if ($names === []) {
            return [];
        }

        $ustajeni = $udalost->ustajeniMoznosti()
            ->whereIn('nazev', $names)
            ->get()
            ->keyBy('nazev');

        $missing = array_values(array_diff($names, $ustajeni->keys()->all()));
        if ($missing !== []) {
            throw new RuntimeException('Missing ustajeni for '.$udalost->nazev.': '.implode(', ', $missing));
        }

        return array_map(
            static fn (string $name): int => (int) $ustajeni[$name]->id,
            $names
        );
    }
}
