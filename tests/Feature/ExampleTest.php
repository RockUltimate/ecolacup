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
}
