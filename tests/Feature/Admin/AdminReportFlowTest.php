<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainModels;
use Tests\TestCase;

class AdminReportFlowTest extends TestCase
{
    use CreatesDomainModels;
    use RefreshDatabase;

    public function test_admin_can_filter_registrations_start_lists_and_service_reports(): void
    {
        $admin = $this->createAdminUser();
        $activeUser = User::factory()->create(['email' => 'active@example.com']);
        $deletedUser = User::factory()->create(['email' => 'deleted@example.com']);

        $udalost = $this->createUdalost(
            moznosti: [
                ['nazev' => 'Trail', 'cena' => 300, 'poradi' => 1],
                ['nazev' => 'Pleasure', 'cena' => 250, 'poradi' => 2],
            ],
            ustajeni: [
                ['nazev' => 'Venkovní box', 'typ' => 'ustajeni', 'cena' => 350],
                ['nazev' => 'Snídaně', 'typ' => 'strava', 'cena' => 120],
            ],
        );

        $activeOsoba = $this->createOsoba($activeUser, ['jmeno' => 'Alice', 'prijmeni' => 'Nova']);
        $activeKun = $this->createKun($activeUser, ['jmeno' => 'Falco']);
        $activePrihlaska = $this->createPrihlaska(
            $udalost,
            $activeUser,
            $activeOsoba,
            $activeKun,
            [$udalost->moznosti[0]->id],
            [$udalost->ustajeniMoznosti[0]->id],
            ['start_cislo' => 2]
        );

        $deletedOsoba = $this->createOsoba($deletedUser, ['jmeno' => 'Petra', 'prijmeni' => 'Smazana']);
        $deletedKun = $this->createKun($deletedUser, ['jmeno' => 'Ghost']);
        $deletedPrihlaska = $this->createPrihlaska(
            $udalost,
            $deletedUser,
            $deletedOsoba,
            $deletedKun,
            [$udalost->moznosti[1]->id],
            [$udalost->ustajeniMoznosti[1]->id],
            ['start_cislo' => 9]
        );
        $deletedPrihlaska->update(['smazana' => true]);
        $deletedPrihlaska->delete();

        $this->actingAs($admin)->get(route('admin.reports.prihlasky', [$udalost, 'q' => 'Nova']))
            ->assertOk()
            ->assertSee('Alice')
            ->assertSee('Falco')
            ->assertDontSee('Smazana')
            ->assertDontSee('Ghost');

        $this->actingAs($admin)->get(route('admin.reports.smazane', [$udalost, 'q' => 'Ghost']))
            ->assertOk()
            ->assertSee('Smazana')
            ->assertSee('Ghost')
            ->assertDontSee('Alice');

        $this->actingAs($admin)->get(route('admin.reports.startky', [
            $udalost,
            'moznost_id' => $udalost->moznosti[0]->id,
            'q' => 'Falco',
        ]))
            ->assertOk()
            ->assertSee('Trail')
            ->assertSee('Falco')
            ->assertDontSee('Ghost');

        $this->actingAs($admin)->get(route('admin.reports.ubytovani', [
            $udalost,
            'typ' => 'ustajeni',
            'q' => 'Falco',
        ]))
            ->assertOk()
            ->assertSee('Venkovní box')
            ->assertSee('Falco')
            ->assertDontSee('Snídaně');

        $this->actingAs($admin)->get(route('admin.reports.exporty', $udalost))
            ->assertOk()
            ->assertSee('Export seznam')
            ->assertSee('Export startky')
            ->assertSee('Export ustájení a ubytování')
            ->assertSee('Bulk PDF ZIP');

        $this->actingAs($admin)->get(route('admin.start-cisla.show', $udalost))
            ->assertOk()
            ->assertSee('Falco')
            ->assertSee((string) $activePrihlaska->start_cislo);
    }

    public function test_admin_can_update_and_normalize_start_numbers(): void
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $udalost = $this->createUdalost(moznosti: [
            ['nazev' => 'Trail', 'cena' => 300, 'poradi' => 1],
        ]);

        $osobaA = $this->createOsoba($user, ['jmeno' => 'Ales']);
        $osobaB = $this->createOsoba($user, ['jmeno' => 'Boris']);
        $osobaC = $this->createOsoba($user, ['jmeno' => 'Cyril']);
        $osobaDeleted = $this->createOsoba($user, ['jmeno' => 'Deleted']);

        $kunA = $this->createKun($user, ['jmeno' => 'Kun A']);
        $kunB = $this->createKun($user, ['jmeno' => 'Kun B']);
        $kunC = $this->createKun($user, ['jmeno' => 'Kun C']);
        $kunDeleted = $this->createKun($user, ['jmeno' => 'Kun Deleted']);

        $prihlaskaA = $this->createPrihlaska($udalost, $user, $osobaA, $kunA, [$udalost->moznosti[0]->id], [], ['start_cislo' => 7]);
        $prihlaskaB = $this->createPrihlaska($udalost, $user, $osobaB, $kunB, [$udalost->moznosti[0]->id], [], ['start_cislo' => 3]);
        $prihlaskaC = $this->createPrihlaska($udalost, $user, $osobaC, $kunC, [$udalost->moznosti[0]->id]);
        $prihlaskaC->update(['start_cislo' => null]);

        $deletedPrihlaska = $this->createPrihlaska($udalost, $user, $osobaDeleted, $kunDeleted, [$udalost->moznosti[0]->id], [], ['start_cislo' => 99]);
        $deletedPrihlaska->update(['smazana' => true]);
        $deletedPrihlaska->delete();

        $this->actingAs($admin)->put(route('admin.reports.start-cislo.update', [$udalost, $prihlaskaA]), [
            'start_cislo' => 5,
        ])->assertRedirect();

        $this->assertDatabaseHas('prihlasky', [
            'id' => $prihlaskaA->id,
            'start_cislo' => 5,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.reports.prihlasky', $udalost))
            ->put(route('admin.reports.start-cislo.update', [$udalost, $prihlaskaB]), [
                'start_cislo' => 5,
            ])
            ->assertRedirect(route('admin.reports.prihlasky', $udalost, absolute: false))
            ->assertInvalid(['start_cislo']);

        $this->assertDatabaseHas('prihlasky', [
            'id' => $prihlaskaB->id,
            'start_cislo' => 3,
        ]);

        $this->actingAs($admin)->put(route('admin.reports.start-cisla.normalize', $udalost))
            ->assertRedirect();

        $this->assertDatabaseHas('prihlasky', ['id' => $prihlaskaB->id, 'start_cislo' => 1]);
        $this->assertDatabaseHas('prihlasky', ['id' => $prihlaskaA->id, 'start_cislo' => 2]);
        $this->assertDatabaseHas('prihlasky', ['id' => $prihlaskaC->id, 'start_cislo' => 3]);
        $this->assertDatabaseHas('prihlasky', ['id' => $deletedPrihlaska->id, 'start_cislo' => 99, 'smazana' => 1]);

        $this->actingAs($admin)
            ->from(route('admin.reports.prihlasky', $udalost))
            ->delete(route('admin.reports.prihlasky.destroy', [$udalost, $prihlaskaA]))
            ->assertRedirect(route('admin.reports.prihlasky', $udalost, absolute: false));

        $this->assertDatabaseHas('prihlasky', [
            'id' => $prihlaskaA->id,
            'smazana' => 1,
        ]);
        $this->assertSoftDeleted('prihlasky', [
            'id' => $prihlaskaA->id,
        ]);
    }

    public function test_non_admin_cannot_access_admin_reports(): void
    {
        $user = User::factory()->create();
        $udalost = $this->createUdalost(moznosti: [
            ['nazev' => 'Trail', 'cena' => 300, 'poradi' => 1],
        ]);
        $osoba = $this->createOsoba($user);
        $kun = $this->createKun($user);
        $prihlaska = $this->createPrihlaska($udalost, $user, $osoba, $kun, [$udalost->moznosti[0]->id]);

        $this->actingAs($user)->get(route('admin.reports.prihlasky', $udalost))->assertForbidden();
        $this->actingAs($user)->get(route('admin.reports.exporty', $udalost))->assertForbidden();
        $this->actingAs($user)->delete(route('admin.reports.prihlasky.destroy', [$udalost, $prihlaska]))->assertForbidden();
        $this->actingAs($user)->put(route('admin.reports.start-cisla.normalize', $udalost))->assertForbidden();
        $this->actingAs($user)->get(route('admin.start-cisla.show', $udalost))->assertForbidden();
    }
}
