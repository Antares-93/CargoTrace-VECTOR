<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('VECTOR');
        $response->assertSee('OUR PARTNERS');
    }

    public function test_auth_pages_render_successfully(): void
    {
        $this->get('/login')->assertStatus(200);
        $this->get('/register')->assertStatus(200);
    }

    public function test_authenticated_dashboard_and_views_render_successfully(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/dashboard')->assertStatus(200);
        $this->actingAs($admin)->get('/incident-desk')->assertStatus(200);
        $this->actingAs($admin)->get('/exceptions')->assertStatus(200);
        $this->actingAs($admin)->get('/shipments')->assertStatus(200);
        $this->actingAs($admin)->get('/carriers')->assertStatus(200);
        $this->actingAs($admin)->get('/locations')->assertStatus(200);
        $this->actingAs($admin)->get('/audit-logs')->assertStatus(200);
    }
}
