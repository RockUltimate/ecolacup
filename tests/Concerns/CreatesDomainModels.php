<?php

namespace Tests\Concerns;

use App\Models\ClenstviCmt;
use App\Models\Kun;
use App\Models\Osoba;
use App\Models\Pleme;
use App\Models\Prihlaska;
use App\Models\Udalost;
use App\Models\User;
use App\Services\PrihlaskaService;

trait CreatesDomainModels
{
    protected function createAdminUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'is_admin' => true,
        ], $attributes));
    }

    protected function createOsoba(User $user, array $attributes = []): Osoba
    {
        return Osoba::query()->create(array_merge([
            'user_id' => $user->id,
            'jmeno' => 'Test',
            'prijmeni' => 'Jezdec',
            'datum_narozeni' => '2000-01-01',
            'staj' => 'Staj A',
            'gdpr_souhlas' => true,
            'gdpr_souhlas_at' => now(),
        ], $attributes));
    }

    protected function createKun(User $user, array $attributes = []): Kun
    {
        return Kun::query()->create(array_merge([
            'user_id' => $user->id,
            'jmeno' => 'Test Horse',
            'plemeno_kod' => 'CMT',
            'plemeno_nazev' => 'Test plemeno',
            'rok_narozeni' => 2015,
            'staj' => 'Staj A',
            'pohlavi' => 'v',
            'ehv_datum' => now()->addMonth()->toDateString(),
            'aie_datum' => now()->addMonth()->toDateString(),
            'chripka_datum' => now()->addMonth()->toDateString(),
        ], $attributes));
    }

    protected function createPleme(array $attributes = []): Pleme
    {
        return Pleme::query()->create(array_merge([
            'kod' => 'CMT',
            'nazev' => 'Cesky mountain horse',
            'poradi' => 1,
        ], $attributes));
    }

    /**
     * @param  array<int, array<string, mixed>>  $moznosti
     * @param  array<int, array<string, mixed>>  $ustajeni
     */
    protected function createUdalost(array $attributes = [], array $moznosti = [], array $ustajeni = []): Udalost
    {
        $udalost = Udalost::query()->create(array_merge([
            'nazev' => 'Test událost',
            'misto' => 'Test areál',
            'datum_zacatek' => now()->addWeek()->toDateString(),
            'datum_konec' => now()->addWeek()->toDateString(),
            'uzavierka_prihlasek' => now()->addDays(5)->toDateString(),
            'kapacita' => null,
            'aktivni' => true,
            'popis' => 'Test popis',
        ], $attributes));

        foreach ($moznosti as $index => $moznost) {
            $udalost->moznosti()->create(array_merge([
                'nazev' => 'Disciplína '.($index + 1),
                'min_vek' => null,
                'cena' => 100,
                'poradi' => $index + 1,
                'je_administrativni_poplatek' => false,
            ], $moznost));
        }

        foreach ($ustajeni as $item) {
            $udalost->ustajeniMoznosti()->create(array_merge([
                'nazev' => 'Ustájení',
                'typ' => 'ustajeni',
                'cena' => 250,
                'kapacita' => null,
            ], $item));
        }

        return $udalost->fresh(['moznosti', 'ustajeniMoznosti']);
    }

    protected function createClenstvi(Osoba $osoba, array $attributes = []): ClenstviCmt
    {
        return ClenstviCmt::query()->create(array_merge([
            'osoba_id' => $osoba->id,
            'organizace_id' => 2,
            'typ_clenstvi' => 'fyzicka_osoba',
            'rok' => 2026,
            'cena' => 500,
            'aktivni' => true,
            'souhlas_gdpr' => true,
            'souhlas_email' => false,
            'souhlas_zverejneni' => false,
        ], $attributes));
    }

    /**
     * @param  array<int, int>  $moznostIds
     * @param  array<int, int>  $ustajeniIds
     * @param  array<string, mixed>  $attributes
     */
    protected function createPrihlaska(
        Udalost $udalost,
        User $user,
        Osoba $osoba,
        Kun $kun,
        array $moznostIds,
        array $ustajeniIds = [],
        array $attributes = []
    ): Prihlaska {
        $prihlaska = Prihlaska::query()->create(array_merge([
            'udalost_id' => $udalost->id,
            'user_id' => $user->id,
            'osoba_id' => $osoba->id,
            'kun_id' => $kun->id,
            'gdpr_souhlas' => true,
            'cena_celkem' => 0,
            'smazana' => false,
        ], $attributes));

        app(PrihlaskaService::class)->syncPrihlaska($prihlaska, $moznostIds, $ustajeniIds);

        return $prihlaska->fresh(['polozky', 'ustajeniChoices']) ?? $prihlaska;
    }
}
