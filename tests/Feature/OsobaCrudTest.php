<?php

namespace Tests\Feature;

use App\Models\Osoba;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainModels;
use Tests\TestCase;

class OsobaCrudTest extends TestCase
{
    use CreatesDomainModels;
    use RefreshDatabase;

    public function test_user_can_create_update_and_delete_person(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('osoby.store'), [
            'jmeno' => 'Anna',
            'prijmeni' => 'Novakova',
            'datum_narozeni' => '2001-05-10',
            'staj' => 'Ranč Sever',
            'gdpr_souhlas' => '1',
        ])->assertRedirect(route('osoby.index', absolute: false));

        $osoba = Osoba::query()->firstOrFail();

        $this->assertDatabaseHas('osoby', [
            'id' => $osoba->id,
            'user_id' => $user->id,
            'jmeno' => 'Anna',
            'prijmeni' => 'Novakova',
            'staj' => 'Ranč Sever',
            'gdpr_souhlas' => 1,
            'gdpr_odvolano' => 0,
        ]);

        $this->actingAs($user)->put(route('osoby.update', $osoba), [
            'jmeno' => 'Anna Marie',
            'prijmeni' => 'Novakova',
            'datum_narozeni' => '2001-05-10',
            'staj' => 'Ranč Jih',
            'gdpr_odvolano' => '1',
        ])->assertRedirect(route('osoby.index', absolute: false));

        $this->assertDatabaseHas('osoby', [
            'id' => $osoba->id,
            'jmeno' => 'Anna Marie',
            'staj' => 'Ranč Jih',
            'gdpr_souhlas' => 0,
            'gdpr_odvolano' => 1,
        ]);

        $this->actingAs($user)->delete(route('osoby.destroy', $osoba))
            ->assertRedirect(route('osoby.index', absolute: false));

        $this->assertSoftDeleted('osoby', ['id' => $osoba->id]);
    }

    public function test_index_shows_only_the_authenticated_users_people(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $myOsoba = $this->createOsoba($user, ['jmeno' => 'Moje', 'prijmeni' => 'Osoba']);
        $this->createOsoba($otherUser, ['jmeno' => 'Cizi', 'prijmeni' => 'Osoba']);

        $this->actingAs($user)->get(route('osoby.index'))
            ->assertOk()
            ->assertSee($myOsoba->jmeno)
            ->assertDontSee('Cizi');
    }

    public function test_user_cannot_manage_someone_elses_person(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $osoba = $this->createOsoba($otherUser);

        $this->actingAs($user)->get(route('osoby.edit', $osoba))->assertForbidden();

        $this->actingAs($user)->put(route('osoby.update', $osoba), [
            'jmeno' => 'Neplatne',
            'prijmeni' => 'Uprava',
            'datum_narozeni' => '2000-01-01',
            'staj' => 'Staj B',
            'gdpr_souhlas' => '1',
        ])->assertForbidden();

        $this->actingAs($user)->delete(route('osoby.destroy', $osoba))->assertForbidden();
    }
}
