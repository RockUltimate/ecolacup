<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainModels;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use CreatesDomainModels;
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $udalost = $this->createUdalost([
            'nazev' => 'Jarni pohar',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Jarni pohar');
        $response->assertSee(route('udalosti.show', $udalost, absolute: false));
        $response->assertSee('Moderní přihlášky na koňské závody');
    }

    public function test_authenticated_user_sees_full_navigation_on_event_pages(): void
    {
        $user = User::factory()->create();
        $udalost = $this->createUdalost();

        $eventIndex = $this->actingAs($user)->get(route('udalosti.index'));
        $eventDetail = $this->actingAs($user)->get(route('udalosti.show', $udalost));

        foreach (['Osoby', 'Koně', 'Přihlášky'] as $label) {
            $eventIndex->assertSee($label);
            $eventDetail->assertSee($label);
        }
    }

    public function test_event_descriptions_render_stored_html(): void
    {
        $udalost = $this->createUdalost([
            'nazev' => 'Html pohar',
            'popis' => '<p>Závod <strong>MT</strong></p><p>Druhý odstavec.</p>',
        ]);

        $index = $this->get(route('udalosti.index'));
        $detail = $this->get(route('udalosti.show', $udalost));

        $index->assertOk();
        $index->assertSee('Závod', false);
        $index->assertSee('<strong>MT</strong>', false);
        $index->assertDontSee('&lt;p&gt;Závod', false);

        $detail->assertOk();
        $detail->assertSee('<p>Závod <strong>MT</strong></p>', false);
        $detail->assertDontSee('&lt;p&gt;Závod', false);
    }

    public function test_admin_sees_homepage_news_editor(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('udalosti.index'));

        $response->assertOk();
        $response->assertSee('Upravit novinku');
        $response->assertSee('Moderní přihlášky na koňské závody');
    }

    public function test_admin_can_update_homepage_news(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)
            ->put(route('admin.homepage-message.update'), [
                'title' => 'Novinky pro závodníky',
                'body' => "V sobotu dorazte o 30 minut dřív.\nPrezentace běží od 8:00.",
            ])
            ->assertRedirect(route('udalosti.index', absolute: false).'#home-news');

        $this->assertDatabaseHas('homepage_messages', [
            'title' => 'Novinky pro závodníky',
            'updated_by' => $admin->id,
        ]);

        $response = $this->get(route('udalosti.index'));

        $response->assertSee('Novinky pro závodníky');
        $response->assertSee('V sobotu dorazte o 30 minut dřív.');
        $response->assertSee('Prezentace běží od 8:00.');
    }

    public function test_non_admin_cannot_update_homepage_news(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('admin.homepage-message.update'), [
                'title' => 'Neoprávněná změna',
                'body' => 'Nemělo by projít.',
            ])
            ->assertForbidden();
    }
}
