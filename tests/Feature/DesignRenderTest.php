<?php

namespace Tests\Feature;

use App\Models\Udalost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_with_site_layout(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('EcolaCup');
        $response->assertSee('site-nav', false);
        $response->assertSee('button-primary', false);
    }

    public function test_event_detail_renders(): void
    {
        $udalost = Udalost::factory()->create(['aktivni' => true]);
        $response = $this->get(route('udalosti.show', $udalost));
        $response->assertStatus(200);
        $response->assertSee($udalost->nazev);
    }

    public function test_login_page_renders_with_glass_card(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('glass-card', false);
        $response->assertSee('EcolaCup');
    }

    public function test_register_page_renders(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('glass-card', false);
    }

    public function test_user_dashboard_requires_auth(): void
    {
        $response = $this->get(route('prihlasky.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_dashboard_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('prihlasky.index'));
        $response->assertStatus(200);
        $response->assertSee('Moje přihlášky');
    }

    public function test_horses_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('kone.index'));
        $response->assertStatus(200);
        $response->assertSee('panel', false);
    }

    public function test_persons_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('osoby.index'));
        $response->assertStatus(200);
        $response->assertSee('panel', false);
    }

    public function test_admin_dashboard_requires_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_dashboard_renders_for_admin(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }
}
