<?php

declare(strict_types=1);

namespace Tests\Feature\Monitor;

use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TestDatabaseSeeder::class);
        $this->loginAsSeededUser('alex@carport.local');
    }

    public function test_connect_screen_renders_pairing_ui(): void
    {
        $response = $this->get(route('monitor.connect'));

        $response->assertOk();
        $response->assertSee('Connect');
        $response->assertSee('Your garage');
        $response->assertSee('Waiting for scan');
        $response->assertSee('Tap Settings → Connectivity → QR Scanner');
        $response->assertDontSee('Simulate scan');
        $response->assertSee('<svg', false);
        $response->assertDontSee('(demo)', false);
        $response->assertDontSee('Refresh code');
        $response->assertDontSee('Read-only · No edits available');
    }

    public function test_vehicle_detail_renders_sample_vehicle(): void
    {
        $response = $this->get(route('monitor.vehicles.show', 'mazda-cx5-2019'));

        $response->assertOk();
        $response->assertSee('2019 Mazda CX-5');
        $response->assertSee('Soul Red Crystal Metallic');
        $response->assertSee('Service log');
        $response->assertSee('4 entries recorded');
        $response->assertSee('User manual');
        $response->assertSee('Service manual');
        $response->assertSee('Attachments');
        $response->assertSee('oil-change-receipt.pdf');
        $response->assertSee('tire-rotation.jpg');
        $response->assertSee(route('monitor.vehicles.attachments.download', [
            'vehicle' => 'mazda-cx5-2019',
            'attachment' => 'oil-change-receipt-2026-03',
        ]), false);
    }

    public function test_vehicle_detail_links_to_service_log(): void
    {
        $response = $this->get(route('monitor.vehicles.show', 'mazda-cx5-2019'));

        $response->assertOk();
        $response->assertSee(route('monitor.vehicles.service-log', 'mazda-cx5-2019'), false);
    }

    public function test_service_log_renders_entries(): void
    {
        $response = $this->get(route('monitor.vehicles.service-log', 'mazda-cx5-2019'));

        $response->assertOk();
        $response->assertSee('Service log');
        $response->assertSee('OIL CHANGE');
        $response->assertSee('TIRE ROTATION');
        $response->assertSee('Search entries');
    }

    public function test_service_log_links_back_to_vehicle(): void
    {
        $response = $this->get(route('monitor.vehicles.service-log', 'mazda-cx5-2019'));

        $response->assertOk();
        $response->assertSee(route('monitor.vehicles.show', 'mazda-cx5-2019'), false);
    }

    public function test_unknown_vehicle_returns_not_found(): void
    {
        $response = $this->get(route('monitor.vehicles.show', 'unknown-vehicle'));

        $response->assertNotFound();
    }
}
