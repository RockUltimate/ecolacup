<?php

namespace Tests\Feature\Admin;

use App\Models\Udalost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesDomainModels;
use Tests\TestCase;

class UdalostManagementTest extends TestCase
{
    use CreatesDomainModels;
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_event_with_pdf(): void
    {
        Storage::fake('public');

        $admin = $this->createAdminUser();
        $originalPdf = UploadedFile::fake()->create('propozice.pdf', 128, 'application/pdf');
        $originalResultsPdf = UploadedFile::fake()->create('vysledky.pdf', 128, 'application/pdf');

        $this->actingAs($admin)->post(route('admin.udalosti.store'), [
            'nazev' => 'Ecola Cup 2026',
            'misto' => 'Olomouc',
            'datum_zacatek' => '1.6.2026',
            'datum_konec' => '3.6.2026',
            'uzavierka_prihlasek' => '20.5.2026',
            'kapacita' => 120,
            'aktivni' => '1',
            'popis' => 'Třídenní závod.',
            'propozice_pdf_upload' => $originalPdf,
            'vysledky_pdf_upload' => $originalResultsPdf,
            'fotoalbum_url' => 'https://example.com/alba/ecola-cup-2026',
        ])->assertRedirect(route('admin.udalosti.index', absolute: false));

        $udalost = Udalost::query()->firstOrFail();
        $originalPath = $udalost->propozice_pdf;
        $originalResultsPath = $udalost->vysledky_pdf;

        $this->assertDatabaseHas('udalosti', [
            'id' => $udalost->id,
            'nazev' => 'Ecola Cup 2026',
            'misto' => 'Olomouc',
            'kapacita' => 120,
            'aktivni' => 1,
            'fotoalbum_url' => 'https://example.com/alba/ecola-cup-2026',
        ]);
        Storage::disk('public')->assertExists($originalPath);
        Storage::disk('public')->assertExists($originalResultsPath);

        $replacementPdf = UploadedFile::fake()->create('propozice-new.pdf', 256, 'application/pdf');
        $replacementResultsPdf = UploadedFile::fake()->create('vysledky-new.pdf', 256, 'application/pdf');

        $this->actingAs($admin)->put(route('admin.udalosti.update', $udalost), [
            'nazev' => 'Ecola Cup 2026 Final',
            'misto' => 'Brno',
            'datum_zacatek' => '2.6.2026',
            'datum_konec' => '4.6.2026',
            'uzavierka_prihlasek' => '25.5.2026',
            'kapacita' => 140,
            'aktivni' => '0',
            'popis' => 'Aktualizované propozice.',
            'propozice_pdf_upload' => $replacementPdf,
            'vysledky_pdf_upload' => $replacementResultsPdf,
            'fotoalbum_url' => 'https://example.com/alba/ecola-cup-2026-final',
        ])->assertRedirect(route('admin.udalosti.edit', $udalost, absolute: false));

        $updatedPath = $udalost->fresh()->propozice_pdf;
        $updatedResultsPath = $udalost->fresh()->vysledky_pdf;

        $this->assertDatabaseHas('udalosti', [
            'id' => $udalost->id,
            'nazev' => 'Ecola Cup 2026 Final',
            'misto' => 'Brno',
            'kapacita' => 140,
            'aktivni' => 0,
            'fotoalbum_url' => 'https://example.com/alba/ecola-cup-2026-final',
        ]);
        Storage::disk('public')->assertMissing($originalPath);
        Storage::disk('public')->assertExists($updatedPath);
        Storage::disk('public')->assertMissing($originalResultsPath);
        Storage::disk('public')->assertExists($updatedResultsPath);

        $this->actingAs($admin)->delete(route('admin.udalosti.destroy', $udalost))
            ->assertRedirect(route('admin.udalosti.index', absolute: false));

        $this->assertSoftDeleted('udalosti', ['id' => $udalost->id]);
    }

    public function test_admin_can_manage_event_options_and_stabling(): void
    {
        $admin = $this->createAdminUser();
        $udalost = $this->createUdalost();

        $this->actingAs($admin)->post(route('admin.udalosti.moznosti.store', $udalost), [
            'nazev' => 'Administrativní poplatek',
            'min_vek' => 0,
            'cena' => 150,
            'poradi' => 9,
            'je_administrativni_poplatek' => '1',
        ])->assertRedirect(route('admin.udalosti.edit', $udalost, absolute: false));

        $moznostId = $udalost->moznosti()->firstOrFail()->id;

        $this->assertDatabaseHas('udalost_moznosti', [
            'id' => $moznostId,
            'udalost_id' => $udalost->id,
            'nazev' => 'Administrativní poplatek',
            'je_administrativni_poplatek' => 1,
        ]);

        $this->actingAs($admin)->post(route('admin.udalosti.ustajeni.store', $udalost), [
            'nazev' => 'Venkovní box',
            'typ' => 'ustajeni',
            'cena' => 350,
            'kapacita' => 25,
        ])->assertRedirect(route('admin.udalosti.edit', $udalost, absolute: false));

        $ustajeniId = $udalost->ustajeniMoznosti()->firstOrFail()->id;

        $this->assertDatabaseHas('udalost_ustajeni', [
            'id' => $ustajeniId,
            'udalost_id' => $udalost->id,
            'nazev' => 'Venkovní box',
            'typ' => 'ustajeni',
            'kapacita' => 25,
        ]);

        $this->actingAs($admin)->delete(route('admin.udalosti.moznosti.destroy', [$udalost, $moznostId]))
            ->assertRedirect(route('admin.udalosti.edit', $udalost, absolute: false));
        $this->actingAs($admin)->delete(route('admin.udalosti.ustajeni.destroy', [$udalost, $ustajeniId]))
            ->assertRedirect(route('admin.udalosti.edit', $udalost, absolute: false));

        $this->assertDatabaseMissing('udalost_moznosti', ['id' => $moznostId]);
        $this->assertDatabaseMissing('udalost_ustajeni', ['id' => $ustajeniId]);
    }

    public function test_non_admin_cannot_access_event_management_routes(): void
    {
        $user = \App\Models\User::factory()->create();
        $udalost = $this->createUdalost();

        $this->actingAs($user)->get(route('admin.udalosti.index'))->assertForbidden();
        $this->actingAs($user)->post(route('admin.udalosti.store'), [
            'nazev' => 'Zakazana akce',
            'misto' => 'Nikde',
            'datum_zacatek' => '1.7.2026',
            'datum_konec' => '1.7.2026',
            'uzavierka_prihlasek' => '20.6.2026',
        ])->assertForbidden();
        $this->actingAs($user)->post(route('admin.udalosti.moznosti.store', $udalost), [
            'nazev' => 'Zakazana disciplína',
            'cena' => 100,
        ])->assertForbidden();
    }

    public function test_admin_event_upload_rejects_non_pdf_file(): void
    {
        Storage::fake('public');

        $admin = $this->createAdminUser();
        $invalidFile = UploadedFile::fake()->create('propozice.txt', 8, 'text/plain');

        $this->actingAs($admin)
            ->from(route('admin.udalosti.create'))
            ->post(route('admin.udalosti.store'), [
                'nazev' => 'Neplatná akce',
                'misto' => 'Olomouc',
                'datum_zacatek' => '1.6.2026',
                'datum_konec' => '3.6.2026',
                'uzavierka_prihlasek' => '20.5.2026',
                'kapacita' => 50,
                'aktivni' => '1',
                'popis' => 'Test',
                'propozice_pdf_upload' => $invalidFile,
                'vysledky_pdf_upload' => $invalidFile,
            ])
            ->assertRedirect(route('admin.udalosti.create', absolute: false))
            ->assertSessionHasErrors(['propozice_pdf_upload', 'vysledky_pdf_upload']);

        $this->assertDatabaseCount('udalosti', 0);
        Storage::disk('public')->assertDirectoryEmpty('propozice');
        Storage::disk('public')->assertDirectoryEmpty('vysledky');
    }

    public function test_admin_event_dates_accept_single_digit_czech_input(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)->post(route('admin.udalosti.store'), [
            'nazev' => 'Lednová akce',
            'misto' => 'Praha',
            'datum_zacatek' => '8.1.2026',
            'datum_konec' => '9.1.2026',
            'uzavierka_prihlasek' => '2.1.2026',
            'aktivni' => '1',
        ])->assertRedirect(route('admin.udalosti.index', absolute: false));

        $this->assertDatabaseHas('udalosti', [
            'nazev' => 'Lednová akce',
            'datum_zacatek' => '2026-01-08 00:00:00',
            'datum_konec' => '2026-01-09 00:00:00',
            'uzavierka_prihlasek' => '2026-01-02 00:00:00',
        ]);
    }
}
