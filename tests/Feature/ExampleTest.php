<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

    public function test_event_index_renders_monthly_calendar_with_navigation(): void
    {
        Carbon::setTestNow('2026-04-08 10:00:00');

        try {
            $this->createUdalost([
                'nazev' => 'Dubnovy pohar',
                'datum_zacatek' => '2026-04-12',
                'datum_konec' => '2026-04-13',
                'uzavierka_prihlasek' => '2026-04-10',
            ]);

            $this->createUdalost([
                'nazev' => 'Kvetnovy pohar',
                'datum_zacatek' => '2026-05-02',
                'datum_konec' => '2026-05-02',
                'uzavierka_prihlasek' => '2026-04-28',
            ]);

            $response = $this->get(route('udalosti.index', ['month' => '2026-04']));

            $response->assertOk();
            $response->assertSee('Měsíční kalendář');
            $response->assertSee('Duben 2026');
            $response->assertSee('Dubnovy pohar');
            $response->assertSee(route('udalosti.index', ['month' => '2026-03'], absolute: false));
            $response->assertSee(route('udalosti.index', ['month' => '2026-05'], absolute: false));
        } finally {
            Carbon::setTestNow();
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

    public function test_past_events_are_automatically_marked_inactive(): void
    {
        Carbon::setTestNow('2026-04-08 10:00:00');

        try {
            $pastEvent = $this->createUdalost([
                'nazev' => 'Archivni akce',
                'datum_zacatek' => '2026-04-01',
                'datum_konec' => '2026-04-05',
                'aktivni' => true,
            ]);

            $futureEvent = $this->createUdalost([
                'nazev' => 'Aktivni akce',
                'datum_zacatek' => '2026-04-20',
                'datum_konec' => '2026-04-21',
                'aktivni' => true,
            ]);

            $this->get(route('udalosti.index'))->assertOk();

            $this->assertDatabaseHas('udalosti', [
                'id' => $pastEvent->id,
                'aktivni' => 0,
            ]);
            $this->assertDatabaseHas('udalosti', [
                'id' => $futureEvent->id,
                'aktivni' => 1,
            ]);
        } finally {
            Carbon::setTestNow();
        }
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

    public function test_homepage_falls_back_to_default_message_when_table_is_missing(): void
    {
        $this->createUdalost([
            'nazev' => 'Zalozni pohar',
        ]);

        Schema::drop('homepage_messages');

        $response = $this->get(route('udalosti.index'));

        $response->assertOk();
        $response->assertSee('Moderní přihlášky na koňské závody');
        $response->assertSee('Veřejný kalendář, přehled uzávěrek, disciplín a kapacit.');
    }

    public function test_homepage_reads_singleton_message_with_fixed_id(): void
    {
        DB::table('homepage_messages')->insert([
            'title' => 'Vedlejší zpráva',
            'body' => 'Tento text se na homepage nesmí zobrazit.',
            'updated_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('udalosti.index'));

        $response->assertOk();
        $response->assertSee('Moderní přihlášky na koňské závody');
        $response->assertDontSee('Vedlejší zpráva');
        $response->assertDontSee('Tento text se na homepage nesmí zobrazit.');
    }

    public function test_admin_updates_fixed_homepage_message_record(): void
    {
        $admin = $this->createAdminUser();

        DB::table('homepage_messages')->insert([
            'title' => 'Vedlejší zpráva',
            'body' => 'Tento text se na homepage nesmí zobrazit.',
            'updated_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->put(route('admin.homepage-message.update'), [
                'title' => 'Jediná novinka',
                'body' => 'Zobrazuje se jen singleton.',
            ])
            ->assertRedirect(route('udalosti.index', absolute: false).'#home-news');

        $this->assertDatabaseHas('homepage_messages', [
            'id' => 1,
            'title' => 'Jediná novinka',
            'updated_by' => $admin->id,
        ]);
        $this->assertDatabaseHas('homepage_messages', [
            'id' => 2,
            'title' => 'Vedlejší zpráva',
        ]);
    }
}
