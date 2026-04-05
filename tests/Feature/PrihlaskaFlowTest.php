<?php

namespace Tests\Feature;

use App\Jobs\SendPrihlaskaEmail;
use App\Models\Prihlaska;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\Concerns\CreatesDomainModels;
use Tests\TestCase;

class PrihlaskaFlowTest extends TestCase
{
    use CreatesDomainModels;
    use RefreshDatabase;

    public function test_user_can_create_registration_and_admin_fee_is_auto_added_for_non_member(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $osoba = $this->createOsoba($user);
        $kun = $this->createKun($user);
        $udalost = $this->createUdalost(
            moznosti: [
                ['nazev' => 'MT Ruka EASY 8+', 'cena' => 300, 'min_vek' => 8],
                ['nazev' => 'Administrativní poplatek', 'cena' => 100, 'je_administrativni_poplatek' => true],
            ],
            ustajeni: [
                ['nazev' => 'Venkovní box', 'typ' => 'ustajeni', 'cena' => 250],
            ],
        );

        $response = $this->actingAs($user)->post(route('prihlasky.store', $udalost), [
            'osoba_id' => $osoba->id,
            'kun_id' => $kun->id,
            'moznosti' => [$udalost->moznosti[0]->id],
            'ustajeni' => [$udalost->ustajeniMoznosti[0]->id],
            'gdpr_souhlas' => '1',
        ]);

        $prihlaska = Prihlaska::query()->firstOrFail();

        $response->assertRedirect(route('prihlasky.show', $prihlaska, absolute: false));
        $this->assertSame(2, $prihlaska->polozky()->count());
        $this->assertDatabaseHas('prihlasky_polozky', [
            'prihlaska_id' => $prihlaska->id,
            'nazev' => 'Administrativní poplatek',
        ]);
        $this->assertEquals(650.0, (float) $prihlaska->fresh()->cena_celkem);
        Queue::assertPushed(SendPrihlaskaEmail::class);
    }

    public function test_admin_fee_is_not_duplicated_for_same_person_and_event(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $osoba = $this->createOsoba($user);
        $prvniKun = $this->createKun($user, ['jmeno' => 'První kůň']);
        $druhyKun = $this->createKun($user, ['jmeno' => 'Druhý kůň']);
        $udalost = $this->createUdalost(moznosti: [
            ['nazev' => 'MT Ruka EASY 8+', 'cena' => 300, 'min_vek' => 8],
            ['nazev' => 'Administrativní poplatek', 'cena' => 100, 'je_administrativni_poplatek' => true],
        ]);

        $this->actingAs($user)->post(route('prihlasky.store', $udalost), [
            'osoba_id' => $osoba->id,
            'kun_id' => $prvniKun->id,
            'moznosti' => [$udalost->moznosti[0]->id, $udalost->moznosti[1]->id],
            'gdpr_souhlas' => '1',
        ])->assertRedirect();

        $this->actingAs($user)->post(route('prihlasky.store', $udalost), [
            'osoba_id' => $osoba->id,
            'kun_id' => $druhyKun->id,
            'moznosti' => [$udalost->moznosti[0]->id, $udalost->moznosti[1]->id],
            'gdpr_souhlas' => '1',
        ])->assertRedirect();

        $druhaPrihlaska = Prihlaska::query()->latest('id')->firstOrFail();

        $this->assertSame(1, $druhaPrihlaska->polozky()->count());
        $this->assertDatabaseMissing('prihlasky_polozky', [
            'prihlaska_id' => $druhaPrihlaska->id,
            'nazev' => 'Administrativní poplatek',
        ]);
        $this->assertEquals(300.0, (float) $druhaPrihlaska->fresh()->cena_celkem);
    }

    public function test_registration_cannot_be_created_after_deadline(): void
    {
        $user = User::factory()->create();
        $osoba = $this->createOsoba($user);
        $kun = $this->createKun($user);
        $udalost = $this->createUdalost(
            ['uzavierka_prihlasek' => now()->subDay()->toDateString()],
            [
                ['nazev' => 'MT Ruka EASY 8+', 'cena' => 300, 'min_vek' => 8],
            ]
        );

        $response = $this->actingAs($user)->post(route('prihlasky.store', $udalost), [
            'osoba_id' => $osoba->id,
            'kun_id' => $kun->id,
            'moznosti' => [$udalost->moznosti[0]->id],
            'gdpr_souhlas' => '1',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('prihlasky', 0);
    }

    public function test_user_cannot_access_someone_elses_registration_routes(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $udalost = $this->createUdalost(
            moznosti: [
                ['nazev' => 'MT Ruka EASY 8+', 'cena' => 300, 'min_vek' => 8],
            ],
            ustajeni: [
                ['nazev' => 'Venkovní box', 'typ' => 'ustajeni', 'cena' => 250],
            ],
        );
        $osoba = $this->createOsoba($owner);
        $kun = $this->createKun($owner);
        $prihlaska = $this->createPrihlaska(
            $udalost,
            $owner,
            $osoba,
            $kun,
            [$udalost->moznosti[0]->id],
            [$udalost->ustajeniMoznosti[0]->id]
        );

        $updatePayload = [
            'osoba_id' => $osoba->id,
            'kun_id' => $kun->id,
            'moznosti' => [$udalost->moznosti[0]->id],
            'ustajeni' => [$udalost->ustajeniMoznosti[0]->id],
            'gdpr_souhlas' => '1',
        ];

        $this->actingAs($intruder)->get(route('prihlasky.show', $prihlaska))->assertForbidden();
        $this->actingAs($intruder)->get(route('prihlasky.edit', $prihlaska))->assertForbidden();
        $this->actingAs($intruder)->put(route('prihlasky.update', $prihlaska), $updatePayload)->assertForbidden();
        $this->actingAs($intruder)->delete(route('prihlasky.destroy', $prihlaska))->assertForbidden();
        $this->actingAs($intruder)->get(route('prihlasky.pdf', $prihlaska))->assertForbidden();

        $this->assertDatabaseHas('prihlasky', [
            'id' => $prihlaska->id,
            'user_id' => $owner->id,
            'smazana' => 0,
        ]);
    }
}
