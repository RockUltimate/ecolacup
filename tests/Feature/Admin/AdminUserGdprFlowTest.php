<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainModels;
use Tests\TestCase;

class AdminUserGdprFlowTest extends TestCase
{
    use CreatesDomainModels;
    use RefreshDatabase;

    public function test_admin_can_purge_user_and_all_related_data(): void
    {
        $admin = $this->createAdminUser();
        $targetUser = User::factory()->create(['email' => 'target@example.com']);
        $otherUser = User::factory()->create(['email' => 'other@example.com']);

        $udalost = $this->createUdalost(moznosti: [
            ['nazev' => 'Trail', 'cena' => 300],
        ]);

        $activeOsoba = $this->createOsoba($targetUser, ['jmeno' => 'Target']);
        $deletedOsoba = $this->createOsoba($targetUser, ['jmeno' => 'Deleted Person']);
        $deletedOsoba->delete();

        $activeKun = $this->createKun($targetUser, ['jmeno' => 'Target Horse']);
        $deletedKun = $this->createKun($targetUser, ['jmeno' => 'Deleted Horse']);
        $deletedKun->delete();

        $activePrihlaska = $this->createPrihlaska($udalost, $targetUser, $activeOsoba, $activeKun, [$udalost->moznosti[0]->id]);
        $deletedPrihlaska = $this->createPrihlaska($udalost, $targetUser, $activeOsoba, $activeKun, [$udalost->moznosti[0]->id], [], ['start_cislo' => 15]);
        $deletedPrihlaska->update(['smazana' => true]);
        $deletedPrihlaska->delete();

        $otherOsoba = $this->createOsoba($otherUser, ['jmeno' => 'Safe']);
        $otherKun = $this->createKun($otherUser, ['jmeno' => 'Safe Horse']);

        $this->actingAs($admin)->delete(route('admin.users.purge', $targetUser))
            ->assertRedirect(route('admin.users.index', absolute: false));

        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
        $this->assertDatabaseMissing('osoby', ['id' => $activeOsoba->id]);
        $this->assertDatabaseMissing('osoby', ['id' => $deletedOsoba->id]);
        $this->assertDatabaseMissing('kone', ['id' => $activeKun->id]);
        $this->assertDatabaseMissing('kone', ['id' => $deletedKun->id]);
        $this->assertDatabaseMissing('prihlasky', ['id' => $activePrihlaska->id]);
        $this->assertDatabaseMissing('prihlasky', ['id' => $deletedPrihlaska->id]);

        $this->assertDatabaseHas('users', ['id' => $otherUser->id]);
        $this->assertDatabaseHas('osoby', ['id' => $otherOsoba->id]);
        $this->assertDatabaseHas('kone', ['id' => $otherKun->id]);
    }

    public function test_admin_cannot_purge_self(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->delete(route('admin.users.purge', $admin))
            ->assertRedirect(route('admin.users.edit', $admin, absolute: false));

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_non_admin_cannot_purge_users(): void
    {
        $user = User::factory()->create();
        $targetUser = User::factory()->create();

        $this->actingAs($user)->delete(route('admin.users.purge', $targetUser))->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $targetUser->id]);
    }
}
