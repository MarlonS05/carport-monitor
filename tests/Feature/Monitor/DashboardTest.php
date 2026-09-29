<?php

declare(strict_types=1);

namespace Tests\Feature\Monitor;

use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TestDatabaseSeeder::class);
        $this->loginAsSeededUser('alex@carport.local');
    }

    public function test_dashboard_renders_garage_overview(): void
    {
        $response = $this->get(route('monitor.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard');
        $response->assertSee('Carport');
        $response->assertSee('Monitor');
        $response->assertSee('2019 Mazda CX-5');
        $response->assertSee('2016 Honda CB500F');
        $response->assertSee('Unassigned Demo Vehicle');
        $response->assertSee('42,350 km');
        $response->assertSee('4 ENTRIES');
        $response->assertSee('Read-only mirror');
        $response->assertSee('V1.0 · Read-only');
        $response->assertDontSee('iPhone 15');
        $response->assertDontSee('Live ·');
    }

    public function test_root_redirects_to_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('monitor.dashboard'));
    }
}
