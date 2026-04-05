<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesDomainModels;
use Tests\TestCase;

class AdminMembershipManagementTest extends TestCase
{
    use CreatesDomainModels;
    use RefreshDatabase;

    public function test_admin_can_replace_membership_scan_and_old_file_is_deleted(): void
    {
        Storage::fake('public');

        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $osoba = $this->createOsoba($user);
        $membership = $this->createClenstvi($osoba, [
            'sken_prihlaska' => 'clenstvi/original.pdf',
            'souhlas_gdpr' => true,
        ]);

        Storage::disk('public')->put('clenstvi/original.pdf', 'original');
        $replacement = UploadedFile::fake()->create('updated.pdf', 64, 'application/pdf');

        $this->actingAs($admin)->put(route('admin.clenstvi.update', $membership), [
            'evidencni_cislo' => 'CMT-2026-01',
            'rok' => 2026,
            'cena' => 500,
            'email' => 'member@example.com',
            'telefon' => '777123123',
            'aktivni' => '1',
            'souhlas_gdpr' => '1',
            'souhlas_email' => '1',
            'souhlas_zverejneni' => '0',
            'sken_prihlaska_upload' => $replacement,
        ])->assertRedirect(route('admin.clenstvi.edit', $membership, absolute: false));

        $updatedPath = $membership->fresh()->sken_prihlaska;

        $this->assertSame('CMT-2026-01', $membership->fresh()->evidencni_cislo);
        Storage::disk('public')->assertMissing('clenstvi/original.pdf');
        Storage::disk('public')->assertExists($updatedPath);
    }

    public function test_admin_membership_update_keeps_existing_scan_when_no_new_file_is_uploaded(): void
    {
        Storage::fake('public');

        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $osoba = $this->createOsoba($user);
        $membership = $this->createClenstvi($osoba, [
            'sken_prihlaska' => 'clenstvi/existing.pdf',
        ]);

        Storage::disk('public')->put('clenstvi/existing.pdf', 'existing');

        $this->actingAs($admin)->put(route('admin.clenstvi.update', $membership), [
            'evidencni_cislo' => 'CMT-KEEP',
            'rok' => 2026,
            'cena' => 550,
            'email' => 'member@example.com',
            'telefon' => '777123123',
            'aktivni' => '1',
            'souhlas_gdpr' => '1',
            'souhlas_email' => '0',
            'souhlas_zverejneni' => '0',
        ])->assertRedirect(route('admin.clenstvi.edit', $membership, absolute: false));

        $this->assertSame('clenstvi/existing.pdf', $membership->fresh()->sken_prihlaska);
        Storage::disk('public')->assertExists('clenstvi/existing.pdf');
    }

    public function test_admin_membership_update_rejects_invalid_file_type(): void
    {
        Storage::fake('public');

        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $osoba = $this->createOsoba($user);
        $membership = $this->createClenstvi($osoba);
        $invalidFile = UploadedFile::fake()->create('scan.txt', 8, 'text/plain');

        $this->actingAs($admin)
            ->from(route('admin.clenstvi.edit', $membership))
            ->put(route('admin.clenstvi.update', $membership), [
                'evidencni_cislo' => 'CMT-BAD',
                'rok' => 2026,
                'cena' => 500,
                'email' => 'member@example.com',
                'telefon' => '777123123',
                'aktivni' => '1',
                'souhlas_gdpr' => '1',
                'sken_prihlaska_upload' => $invalidFile,
            ])
            ->assertRedirect(route('admin.clenstvi.edit', $membership, absolute: false))
            ->assertSessionHasErrors('sken_prihlaska_upload');
    }

    public function test_non_admin_cannot_update_membership_in_admin(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $osoba = $this->createOsoba($owner);
        $membership = $this->createClenstvi($osoba);

        $this->actingAs($user)->put(route('admin.clenstvi.update', $membership), [
            'rok' => 2026,
            'cena' => 500,
        ])->assertForbidden();
    }
}
