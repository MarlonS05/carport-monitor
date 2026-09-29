<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\MileageUnit;
use App\Models\ServiceItem;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class VehicleModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_vehicle_resolves_route_key_by_external_id(): void
    {
        $vehicle = Vehicle::factory()->create([
            'external_id' => 'mazda-cx5-2019',
        ]);

        $this->assertSame('mazda-cx5-2019', $vehicle->getRouteKey());
    }

    public function test_vehicle_has_service_items(): void
    {
        $vehicle = Vehicle::factory()
            ->has(ServiceItem::factory()->count(3), 'serviceItems')
            ->create();

        $this->assertCount(3, $vehicle->serviceItems);
    }

    public function test_vehicle_stores_manual_urls(): void
    {
        $vehicle = Vehicle::factory()->create([
            'user_manual_url' => 'https://example.com/user-manual',
            'service_manual_url' => 'https://example.com/service-manual',
        ]);

        $this->assertSame('https://example.com/user-manual', $vehicle->user_manual_url);
        $this->assertSame('https://example.com/service-manual', $vehicle->service_manual_url);
    }

    public function test_vehicle_normalizes_manual_urls_without_scheme_for_href(): void
    {
        $vehicle = Vehicle::factory()->create([
            'user_manual_url' => 'louis.de',
            'service_manual_url' => 'www.example.com/manual',
        ]);

        $this->assertSame('https://louis.de', $vehicle->userManualHref());
        $this->assertSame('https://www.example.com/manual', $vehicle->serviceManualHref());
    }

    public function test_vehicle_preserves_manual_urls_with_scheme_for_href(): void
    {
        $vehicle = Vehicle::factory()->create([
            'user_manual_url' => 'http://example.com/user-manual',
            'service_manual_url' => 'https://example.com/service-manual',
        ]);

        $this->assertSame('http://example.com/user-manual', $vehicle->userManualHref());
        $this->assertSame('https://example.com/service-manual', $vehicle->serviceManualHref());
    }

    public function test_vehicle_returns_null_href_for_local_manual_paths(): void
    {
        $vehicle = Vehicle::factory()->create([
            'user_manual_url' => 'My local manual.pdf',
            'service_manual_url' => '/var/manuals/service.pdf',
        ]);

        $this->assertNull($vehicle->userManualHref());
        $this->assertNull($vehicle->serviceManualHref());
    }

    public function test_vehicle_casts_mileage_unit_to_enum(): void
    {
        $vehicle = Vehicle::factory()->create([
            'mileage_unit' => MileageUnit::Miles,
        ]);

        $this->assertSame(MileageUnit::Miles, $vehicle->mileage_unit);
    }

    public function test_vehicle_builds_maintenance_schedule_image_data_uri(): void
    {
        $pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

        $vehicle = Vehicle::factory()->create([
            'maintenance_schedule_image' => $pngBase64,
        ]);

        $this->assertSame(
            'data:image/png;base64,'.$pngBase64,
            $vehicle->maintenanceScheduleImageDataUri(),
        );
    }

    public function test_vehicle_returns_null_data_uri_when_image_missing(): void
    {
        $vehicle = Vehicle::factory()->create([
            'maintenance_schedule_image' => null,
        ]);

        $this->assertNull($vehicle->maintenanceScheduleImageDataUri());
    }
}
