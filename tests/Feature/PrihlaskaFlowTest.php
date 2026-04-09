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

    public function test_first_registration_for_person_automatically_includes_admin_fee(): void
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
        $this->actingAs($user)
            ->get(route('prihlasky.show', $prihlaska))
            ->assertOk()
            ->assertSee('Vytvořit novou přihlášku')
            ->assertSee(route('prihlasky.create', $udalost, absolute: false));
        Queue::assertPushed(SendPrihlaskaEmail::class);
    }

    public function test_additional_registration_for_same_person_does_not_charge_admin_fee_again(): void
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
            'moznosti' => [$udalost->moznosti[0]->id],
            'gdpr_souhlas' => '1',
        ])->assertRedirect();

        $this->actingAs($user)->post(route('prihlasky.store', $udalost), [
            'osoba_id' => $osoba->id,
            'kun_id' => $druhyKun->id,
            'moznosti' => [$udalost->moznosti[0]->id],
            'gdpr_souhlas' => '1',
        ])->assertRedirect();

        $prvniPrihlaska = Prihlaska::query()->orderBy('id')->firstOrFail();
        $druhaPrihlaska = Prihlaska::query()->latest('id')->firstOrFail();

        $this->assertDatabaseHas('prihlasky_polozky', [
            'prihlaska_id' => $prvniPrihlaska->id,
            'nazev' => 'Administrativní poplatek',
        ]);
        $this->assertDatabaseMissing('prihlasky_polozky', [
            'prihlaska_id' => $druhaPrihlaska->id,
            'nazev' => 'Administrativní poplatek',
        ]);
        $this->assertEquals(300.0, (float) $druhaPrihlaska->fresh()->cena_celkem);
    }

    public function test_admin_fee_is_reassigned_when_fee_bearing_registration_is_deleted(): void
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

        $prvniPrihlaska = $this->createPrihlaska($udalost, $user, $osoba, $prvniKun, [$udalost->moznosti[0]->id]);
        $druhaPrihlaska = $this->createPrihlaska($udalost, $user, $osoba, $druhyKun, [$udalost->moznosti[0]->id]);

        $this->actingAs($user)
            ->delete(route('prihlasky.destroy', $prvniPrihlaska))
            ->assertRedirect(route('prihlasky.index', absolute: false));

        $this->assertDatabaseMissing('prihlasky_polozky', [
            'prihlaska_id' => $prvniPrihlaska->id,
            'nazev' => 'Administrativní poplatek',
        ]);
        $this->assertDatabaseHas('prihlasky_polozky', [
            'prihlaska_id' => $druhaPrihlaska->id,
            'nazev' => 'Administrativní poplatek',
        ]);
        $this->assertEquals(400.0, (float) $druhaPrihlaska->fresh()->cena_celkem);
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

    public function test_user_can_see_discipline_image_and_pdf_links(): void
    {
        $user = User::factory()->create();
        $this->createOsoba($user);
        $this->createKun($user);
        $udalost = $this->createUdalost(
            moznosti: [
                [
                    'nazev' => 'Trail',
                    'cena' => 300,
                    'foto_path' => 'disciplines/trail-photo.webp',
                    'pdf_path' => 'disciplines/trail-info.pdf',
                ],
            ],
        );

        $this->actingAs($user)
            ->get(route('prihlasky.create', $udalost))
            ->assertOk()
            ->assertSee('Zobrazit obrázek')
            ->assertSee('Stáhnout PDF')
            ->assertSee('trail-photo.webp')
            ->assertSee('trail-info.pdf');

        $this->get(route('udalosti.show', $udalost))
            ->assertOk()
            ->assertSee('Zobrazit obrázek')
            ->assertSee('Stáhnout PDF')
            ->assertSee('trail-photo.webp')
            ->assertSee('trail-info.pdf');
    }

    public function test_registration_update_queues_updated_confirmation_email(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $osoba = $this->createOsoba($user);
        $kun = $this->createKun($user);
        $udalost = $this->createUdalost(
            moznosti: [
                ['nazev' => 'MT Ruka EASY 8+', 'cena' => 300, 'min_vek' => 8],
                ['nazev' => 'Trail', 'cena' => 250, 'min_vek' => 8],
                ['nazev' => 'Administrativní poplatek', 'cena' => 100, 'je_administrativni_poplatek' => true],
            ],
            ustajeni: [
                ['nazev' => 'Venkovní box', 'typ' => 'ustajeni', 'cena' => 250],
            ],
        );

        $prihlaska = $this->createPrihlaska(
            $udalost,
            $user,
            $osoba,
            $kun,
            [$udalost->moznosti[0]->id],
            [$udalost->ustajeniMoznosti[0]->id]
        );

        $this->actingAs($user)
            ->put(route('prihlasky.update', $prihlaska), [
                'osoba_id' => $osoba->id,
                'kun_id' => $kun->id,
                'moznosti' => [$udalost->moznosti[1]->id],
                'ustajeni' => [],
                'gdpr_souhlas' => '1',
            ])
            ->assertRedirect(route('prihlasky.show', $prihlaska, absolute: false));

        Queue::assertPushed(SendPrihlaskaEmail::class, function (SendPrihlaskaEmail $job) use ($prihlaska) {
            return (int) $job->prihlaska->id === (int) $prihlaska->id
                && $job->mode === 'updated'
                && $job->notifyAdmin === false;
        });
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
