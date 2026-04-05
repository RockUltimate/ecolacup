<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainModels;
use Tests\TestCase;

class AdminExportTest extends TestCase
{
    use CreatesDomainModels;
    use RefreshDatabase;

    public function test_admin_can_download_registration_list_export(): void
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $osoba = $this->createOsoba($user, ['jmeno' => 'Pavla', 'prijmeni' => 'Cihlova']);
        $kun = $this->createKun($user, ['jmeno' => 'Chance']);
        $udalost = $this->createUdalost(moznosti: [
            ['nazev' => 'MT Ruka EASY 8+', 'cena' => 300],
            ['nazev' => 'Administrativní poplatek', 'cena' => 100, 'je_administrativni_poplatek' => true],
        ]);

        $this->createPrihlaska($udalost, $user, $osoba, $kun, [$udalost->moznosti[0]->id]);

        $response = $this->actingAs($admin)->get(route('admin.reports.export.seznam', $udalost));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel');
        $response->assertSeeText('Pavla');
        $response->assertSeeText('Chance');
    }

    public function test_admin_can_download_gdpr_export_for_user(): void
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create([
            'jmeno' => 'Lucie',
            'prijmeni' => 'Novakova',
            'email' => 'lucie@example.test',
        ]);
        $osoba = $this->createOsoba($user);
        $kun = $this->createKun($user);
        $udalost = $this->createUdalost(moznosti: [
            ['nazev' => 'MT Ruka EASY 8+', 'cena' => 300],
        ]);

        $this->createClenstvi($osoba);
        $this->createPrihlaska($udalost, $user, $osoba, $kun, [$udalost->moznosti[0]->id]);

        $response = $this->actingAs($admin)->get(route('admin.users.gdpr-export', $user));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertSeeText('lucie@example.test');
        $response->assertSeeText('prihlaska');
        $response->assertSeeText('clenstvi_cmt');
    }

    public function test_non_admin_cannot_access_admin_export(): void
    {
        $user = User::factory()->create();
        $udalost = $this->createUdalost();

        $response = $this->actingAs($user)->get(route('admin.reports.export.seznam', $udalost));

        $response->assertForbidden();
    }
}
