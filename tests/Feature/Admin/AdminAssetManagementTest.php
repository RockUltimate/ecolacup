<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainModels;
use Tests\TestCase;

class AdminAssetManagementTest extends TestCase
{
    use CreatesDomainModels;
    use RefreshDatabase;

    public function test_admin_can_view_all_horses_and_persons_across_users(): void
    {
        $admin = $this->createAdminUser();
        $firstUser = \App\Models\User::factory()->create([
            'jmeno' => 'Alena',
            'prijmeni' => 'Nováková',
            'email' => 'alena@example.test',
        ]);
        $secondUser = \App\Models\User::factory()->create([
            'jmeno' => 'Petr',
            'prijmeni' => 'Svoboda',
            'email' => 'petr@example.test',
        ]);

        $firstHorse = $this->createKun($firstUser, ['jmeno' => 'Blesk']);
        $secondHorse = $this->createKun($secondUser, ['jmeno' => 'Meteor']);
        $firstPerson = $this->createOsoba($firstUser, ['jmeno' => 'Eva', 'prijmeni' => 'Jezdkyně']);
        $secondPerson = $this->createOsoba($secondUser, ['jmeno' => 'Jan', 'prijmeni' => 'Trenér']);

        $this->actingAs($admin)
            ->get(route('admin.kone.index'))
            ->assertOk()
            ->assertSee($firstHorse->jmeno)
            ->assertSee($secondHorse->jmeno)
            ->assertSee('alena@example.test')
            ->assertSee('petr@example.test');

        $this->actingAs($admin)
            ->get(route('admin.osoby.index'))
            ->assertOk()
            ->assertSee($firstPerson->prijmeni.' '.$firstPerson->jmeno)
            ->assertSee($secondPerson->prijmeni.' '.$secondPerson->jmeno)
            ->assertSee('alena@example.test')
            ->assertSee('petr@example.test');
    }

    public function test_admin_can_edit_horse_and_person_records(): void
    {
        $admin = $this->createAdminUser();
        $user = \App\Models\User::factory()->create();
        $horse = $this->createKun($user, ['jmeno' => 'Blesk']);
        $person = $this->createOsoba($user, ['jmeno' => 'Eva', 'prijmeni' => 'Jezdkyně']);

        $this->actingAs($admin)
            ->put(route('admin.kone.update', $horse), [
                'jmeno' => 'Blesk Final',
                'plemeno_kod' => 'QH',
                'plemeno_nazev' => 'Quarter Horse',
                'plemeno_vlastni' => null,
                'rok_narozeni' => 2016,
                'staj' => 'Nova staj',
                'pohlavi' => 'v',
                'cislo_prukazu' => 'ABC-123',
                'cislo_hospodarstvi' => 'HOSP-1',
                'majitel_jmeno_adresa' => 'Petr Novak, Praha',
            ])
            ->assertRedirect(route('admin.kone.edit', $horse, absolute: false));

        $this->assertDatabaseHas('kone', [
            'id' => $horse->id,
            'jmeno' => 'Blesk Final',
            'staj' => 'Nova staj',
            'cislo_prukazu' => 'ABC-123',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.osoby.update', $person), [
                'jmeno' => 'Eva',
                'prijmeni' => 'Final',
                'datum_narozeni' => '01.01.2000',
                'staj' => 'Nova staj',
                'gdpr_souhlas' => '1',
            ])
            ->assertRedirect(route('admin.osoby.edit', $person, absolute: false));

        $this->assertDatabaseHas('osoby', [
            'id' => $person->id,
            'prijmeni' => 'Final',
            'staj' => 'Nova staj',
        ]);
    }

    public function test_admin_can_apply_selected_horse_description_to_duplicates(): void
    {
        $admin = $this->createAdminUser();
        $firstUser = \App\Models\User::factory()->create();
        $secondUser = \App\Models\User::factory()->create();

        $sourceHorse = $this->createKun($firstUser, [
            'jmeno' => 'Blesk',
            'plemeno_nazev' => 'Quarter Horse',
            'rok_narozeni' => 2017,
            'staj' => 'Staj A',
            'pohlavi' => 'v',
            'cislo_prukazu' => 'PR-001',
            'cislo_hospodarstvi' => 'H-001',
            'majitel_jmeno_adresa' => 'Jan Novak',
        ]);
        $duplicateHorse = $this->createKun($secondUser, [
            'jmeno' => 'Blesk',
            'plemeno_nazev' => 'Neznáme',
            'rok_narozeni' => 2012,
            'staj' => 'Staj B',
            'pohlavi' => 'k',
            'cislo_prukazu' => 'OLD-999',
            'cislo_hospodarstvi' => 'OLD-H',
            'majitel_jmeno_adresa' => 'Stary majitel',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.kone.duplicates.sync'), [
                'source_kun_id' => $sourceHorse->id,
            ])
            ->assertRedirect(route('admin.kone.index', absolute: false));

        $this->assertDatabaseHas('kone', [
            'id' => $duplicateHorse->id,
            'user_id' => $secondUser->id,
            'jmeno' => 'Blesk',
            'plemeno_nazev' => 'Quarter Horse',
            'rok_narozeni' => 2017,
            'staj' => 'Staj A',
            'pohlavi' => 'v',
            'cislo_prukazu' => 'PR-001',
            'cislo_hospodarstvi' => 'H-001',
            'majitel_jmeno_adresa' => 'Jan Novak',
        ]);
    }

    public function test_non_admin_cannot_access_admin_horse_and_person_management(): void
    {
        $user = \App\Models\User::factory()->create();
        $owner = \App\Models\User::factory()->create();
        $horse = $this->createKun($owner);
        $person = $this->createOsoba($owner);

        $this->actingAs($user)->get(route('admin.kone.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.osoby.index'))->assertForbidden();
        $this->actingAs($user)->put(route('admin.kone.update', $horse), [
            'jmeno' => 'Neplatne',
            'plemeno_kod' => 'QH',
            'plemeno_nazev' => 'Quarter Horse',
            'plemeno_vlastni' => null,
            'rok_narozeni' => 2016,
            'staj' => 'Staj',
            'pohlavi' => 'v',
            'cislo_prukazu' => null,
            'cislo_hospodarstvi' => null,
            'majitel_jmeno_adresa' => null,
        ])->assertForbidden();
        $this->actingAs($user)->put(route('admin.osoby.update', $person), [
            'jmeno' => 'Test',
            'prijmeni' => 'Zakazany',
            'datum_narozeni' => '01.01.2000',
            'staj' => 'Staj',
            'gdpr_souhlas' => '1',
        ])->assertForbidden();
    }
}
