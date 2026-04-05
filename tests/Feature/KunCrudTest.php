<?php

namespace Tests\Feature;

use App\Models\Kun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainModels;
use Tests\TestCase;

class KunCrudTest extends TestCase
{
    use CreatesDomainModels;
    use RefreshDatabase;

    public function test_user_can_create_update_and_delete_horse(): void
    {
        $user = User::factory()->create();
        $this->createPleme();

        $this->actingAs($user)->post(route('kone.store'), [
            'jmeno' => 'Argo',
            'plemeno_kod' => 'CMT',
            'plemeno_nazev' => 'Cesky mountain horse',
            'plemeno_vlastni' => null,
            'rok_narozeni' => 2017,
            'staj' => 'Ranč Sever',
            'pohlavi' => 'v',
            'ehv_datum' => now()->addMonth()->toDateString(),
            'aie_datum' => now()->addMonth()->toDateString(),
            'chripka_datum' => now()->addMonth()->toDateString(),
            'cislo_prukazu' => 'ABC-123',
            'cislo_hospodarstvi' => 'HZ-77',
            'majitel_jmeno_adresa' => 'Test Owner, Praha',
        ])->assertRedirect(route('kone.index', absolute: false));

        $kun = Kun::query()->firstOrFail();

        $this->assertDatabaseHas('kone', [
            'id' => $kun->id,
            'user_id' => $user->id,
            'jmeno' => 'Argo',
            'plemeno_kod' => 'CMT',
            'staj' => 'Ranč Sever',
            'pohlavi' => 'v',
            'cislo_prukazu' => 'ABC-123',
        ]);

        $this->actingAs($user)->put(route('kone.update', $kun), [
            'jmeno' => 'Argo II',
            'plemeno_kod' => 'CMT',
            'plemeno_nazev' => 'Cesky mountain horse',
            'plemeno_vlastni' => 'Linie A',
            'rok_narozeni' => 2017,
            'staj' => 'Ranč Jih',
            'pohlavi' => 'k',
            'ehv_datum' => now()->addMonths(2)->toDateString(),
            'aie_datum' => now()->addMonths(2)->toDateString(),
            'chripka_datum' => now()->addMonths(2)->toDateString(),
            'cislo_prukazu' => 'XYZ-999',
            'cislo_hospodarstvi' => 'HZ-99',
            'majitel_jmeno_adresa' => 'Updated Owner, Brno',
        ])->assertRedirect(route('kone.index', absolute: false));

        $this->assertDatabaseHas('kone', [
            'id' => $kun->id,
            'jmeno' => 'Argo II',
            'plemeno_vlastni' => 'Linie A',
            'staj' => 'Ranč Jih',
            'pohlavi' => 'k',
            'cislo_prukazu' => 'XYZ-999',
        ]);

        $this->actingAs($user)->delete(route('kone.destroy', $kun))
            ->assertRedirect(route('kone.index', absolute: false));

        $this->assertSoftDeleted('kone', ['id' => $kun->id]);
    }

    public function test_index_shows_only_the_authenticated_users_horses(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $myKun = $this->createKun($user, ['jmeno' => 'Moje Kobyla']);
        $this->createKun($otherUser, ['jmeno' => 'Cizi Kůň']);

        $this->actingAs($user)->get(route('kone.index'))
            ->assertOk()
            ->assertSee($myKun->jmeno)
            ->assertDontSee('Cizi Kůň');
    }

    public function test_user_cannot_manage_someone_elses_horse(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $kun = $this->createKun($otherUser);

        $this->actingAs($user)->get(route('kone.edit', $kun))->assertForbidden();

        $this->actingAs($user)->put(route('kone.update', $kun), [
            'jmeno' => 'Neplatny kůň',
            'plemeno_kod' => 'CMT',
            'plemeno_nazev' => 'Test plemeno',
            'rok_narozeni' => 2015,
            'staj' => 'Cizi staj',
            'pohlavi' => 'v',
        ])->assertForbidden();

        $this->actingAs($user)->delete(route('kone.destroy', $kun))->assertForbidden();
    }
}
