<?php

namespace Tests\Feature;

use App\Models\ClenstviCmt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesDomainModels;
use Tests\TestCase;

class ClenstviCmtFlowTest extends TestCase
{
    use CreatesDomainModels;
    use RefreshDatabase;

    public function test_first_membership_adds_new_member_admin_fee(): void
    {
        $user = User::factory()->create([
            'telefon' => '777123123',
        ]);
        $osoba = $this->createOsoba($user);

        $response = $this->actingAs($user)->post(route('clenstvi-cmt.store'), [
            'osoba_id' => $osoba->id,
            'typ_clenstvi' => 'fyzicka_osoba',
            'rok' => 2026,
            'cena' => 0,
            'telefon' => '777123123',
            'email' => $user->email,
            'souhlas_gdpr' => '1',
        ]);

        $response->assertRedirect(route('clenstvi-cmt.index', absolute: false));
        $clenstvi = ClenstviCmt::query()->firstOrFail();

        $this->assertEquals(600.0, (float) $clenstvi->cena);
    }

    public function test_membership_can_be_renewed_for_next_year(): void
    {
        $user = User::factory()->create();
        $osoba = $this->createOsoba($user);
        $clenstvi = $this->createClenstvi($osoba, [
            'rok' => 2026,
            'aktivni' => true,
            'cena' => 500,
        ]);

        $response = $this->actingAs($user)->post(route('clenstvi-cmt.renew', $clenstvi));

        $response->assertRedirect(route('clenstvi-cmt.index', absolute: false));
        $this->assertDatabaseHas('clenstvi_cmt', [
            'osoba_id' => $osoba->id,
            'rok' => 2026,
            'aktivni' => 0,
        ]);
        $this->assertDatabaseHas('clenstvi_cmt', [
            'osoba_id' => $osoba->id,
            'rok' => 2027,
            'aktivni' => 1,
        ]);
    }

    public function test_membership_creation_requires_gdpr_consent(): void
    {
        $user = User::factory()->create();
        $osoba = $this->createOsoba($user);

        $this->actingAs($user)
            ->from(route('clenstvi-cmt.create'))
            ->post(route('clenstvi-cmt.store'), [
                'osoba_id' => $osoba->id,
                'typ_clenstvi' => 'fyzicka_osoba',
                'rok' => 2026,
                'cena' => 0,
                'telefon' => '777123123',
                'email' => $user->email,
            ])
            ->assertRedirect(route('clenstvi-cmt.create', absolute: false))
            ->assertSessionHasErrors('souhlas_gdpr');

        $this->assertDatabaseCount('clenstvi_cmt', 0);
    }

    public function test_membership_scan_rejects_invalid_file_type(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $osoba = $this->createOsoba($user);
        $invalidFile = UploadedFile::fake()->create('membership.txt', 8, 'text/plain');

        $this->actingAs($user)
            ->from(route('clenstvi-cmt.create'))
            ->post(route('clenstvi-cmt.store'), [
                'osoba_id' => $osoba->id,
                'typ_clenstvi' => 'fyzicka_osoba',
                'rok' => 2026,
                'cena' => 0,
                'telefon' => '777123123',
                'email' => $user->email,
                'souhlas_gdpr' => '1',
                'sken_prihlaska_upload' => $invalidFile,
            ])
            ->assertRedirect(route('clenstvi-cmt.create', absolute: false))
            ->assertSessionHasErrors('sken_prihlaska_upload');

        $this->assertDatabaseCount('clenstvi_cmt', 0);
        Storage::disk('public')->assertDirectoryEmpty('clenstvi');
    }
}
