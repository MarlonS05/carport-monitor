<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Mobile;
use App\Models\ServiceItem;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class SyncServiceItemsTest extends TestCase
{
    use RefreshDatabase;

    private const string UPDATED_AT = '2026-07-08T10:15:30Z';

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function serviceItem(array $payload): array
    {
        return array_merge(['updated_at' => self::UPDATED_AT], $payload);
    }

    public function test_sync_creates_new_service_items_for_mobile(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();
        $serviceItemId = (string) Str::uuid();

        Vehicle::factory()->create([
            'external_id' => $vehicleId,
            'mobile_id' => $mobile->id,
        ]);

        $response = $this->postJson('/api/service-items/sync', [
            'service_items' => [
                $this->serviceItem([
                    'id' => $serviceItemId,
                    'vehicle_id' => $vehicleId,
                    'title' => 'OIL CHANGE',
                    'description' => 'Full synthetic',
                    'mileage' => 42_350,
                    'mileage_unit' => 'km',
                    'occurred_at' => '2026-03-04',
                ]),
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'created' => 1,
            'updated' => 0,
            'ignored' => 0,
            'items' => [
                ['id' => $serviceItemId, 'status' => 'created'],
            ],
        ]);

        $this->assertDatabaseHas('service_items', [
            'external_id' => $serviceItemId,
            'mobile_id' => $mobile->id,
            'title' => 'OIL CHANGE',
            'mileage' => 42_350,
        ]);
    }

    public function test_sync_updates_service_item_when_mobile_matches(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();
        $serviceItemId = (string) Str::uuid();

        $vehicle = Vehicle::factory()->create([
            'external_id' => $vehicleId,
            'mobile_id' => $mobile->id,
        ]);

        ServiceItem::factory()->create([
            'external_id' => $serviceItemId,
            'vehicle_id' => $vehicle->id,
            'mobile_id' => $mobile->id,
            'title' => 'OLD TITLE',
            'mileage' => 10_000,
        ]);

        $response = $this->postJson('/api/service-items/sync', [
            'service_items' => [
                $this->serviceItem([
                    'id' => $serviceItemId,
                    'vehicle_id' => $vehicleId,
                    'title' => 'NEW TITLE',
                    'mileage' => 20_000,
                    'mileage_unit' => 'km',
                    'occurred_at' => '2026-04-01',
                ]),
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'created' => 0,
            'updated' => 1,
            'ignored' => 0,
        ]);

        $this->assertDatabaseHas('service_items', [
            'external_id' => $serviceItemId,
            'mobile_id' => $mobile->id,
            'title' => 'NEW TITLE',
            'mileage' => 20_000,
        ]);
        $this->assertDatabaseCount('service_items', 1);
    }

    public function test_sync_ignores_service_item_owned_by_different_mobile(): void
    {
        $owner = Mobile::factory()->create();
        $other = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();
        $serviceItemId = (string) Str::uuid();

        $vehicle = Vehicle::factory()->create([
            'external_id' => $vehicleId,
            'mobile_id' => $owner->id,
        ]);

        ServiceItem::factory()->create([
            'external_id' => $serviceItemId,
            'vehicle_id' => $vehicle->id,
            'mobile_id' => $owner->id,
            'title' => 'OWNER TITLE',
            'mileage' => 10_000,
        ]);

        $response = $this->postJson('/api/service-items/sync', [
            'service_items' => [
                $this->serviceItem([
                    'id' => $serviceItemId,
                    'vehicle_id' => $vehicleId,
                    'title' => 'ATTEMPTED OVERWRITE',
                    'mileage' => 99_999,
                    'mileage_unit' => 'km',
                    'occurred_at' => '2026-05-01',
                ]),
            ],
        ], [
            'X-Mobile-Id' => $other->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'created' => 0,
            'updated' => 0,
            'ignored' => 1,
        ]);

        $this->assertDatabaseHas('service_items', [
            'external_id' => $serviceItemId,
            'mobile_id' => $owner->id,
            'title' => 'OWNER TITLE',
            'mileage' => 10_000,
        ]);
    }

    public function test_sync_ignores_service_item_when_vehicle_not_owned_by_mobile(): void
    {
        $mobile = Mobile::factory()->create();
        $other = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();
        $serviceItemId = (string) Str::uuid();

        Vehicle::factory()->create([
            'external_id' => $vehicleId,
            'mobile_id' => $other->id,
        ]);

        $response = $this->postJson('/api/service-items/sync', [
            'service_items' => [
                $this->serviceItem([
                    'id' => $serviceItemId,
                    'vehicle_id' => $vehicleId,
                    'title' => 'OIL CHANGE',
                    'mileage' => 1_000,
                    'mileage_unit' => 'km',
                    'occurred_at' => '2026-03-04',
                ]),
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'created' => 0,
            'updated' => 0,
            'ignored' => 1,
        ]);

        $this->assertDatabaseMissing('service_items', [
            'external_id' => $serviceItemId,
        ]);
    }

    public function test_sync_handles_bulk_array_with_mixed_outcomes(): void
    {
        $mobile = Mobile::factory()->create();
        $other = Mobile::factory()->create();

        $vehicleId = (string) Str::uuid();
        $otherVehicleId = (string) Str::uuid();
        $createId = (string) Str::uuid();
        $updateId = (string) Str::uuid();
        $ignoreId = (string) Str::uuid();

        $vehicle = Vehicle::factory()->create([
            'external_id' => $vehicleId,
            'mobile_id' => $mobile->id,
        ]);

        Vehicle::factory()->create([
            'external_id' => $otherVehicleId,
            'mobile_id' => $other->id,
        ]);

        ServiceItem::factory()->create([
            'external_id' => $updateId,
            'vehicle_id' => $vehicle->id,
            'mobile_id' => $mobile->id,
            'title' => 'Before update',
            'mileage' => 1_000,
        ]);

        ServiceItem::factory()->create([
            'external_id' => $ignoreId,
            'vehicle_id' => $vehicle->id,
            'mobile_id' => $other->id,
            'title' => 'Other mobile item',
            'mileage' => 2_000,
        ]);

        $response = $this->postJson('/api/service-items/sync', [
            'service_items' => [
                $this->serviceItem([
                    'id' => $createId,
                    'vehicle_id' => $vehicleId,
                    'title' => 'New item',
                    'mileage' => 3_000,
                    'mileage_unit' => 'km',
                    'occurred_at' => '2026-01-01',
                ]),
                $this->serviceItem([
                    'id' => $updateId,
                    'vehicle_id' => $vehicleId,
                    'title' => 'After update',
                    'mileage' => 4_000,
                    'mileage_unit' => 'km',
                    'occurred_at' => '2026-02-01',
                ]),
                $this->serviceItem([
                    'id' => $ignoreId,
                    'vehicle_id' => $vehicleId,
                    'title' => 'Should not apply',
                    'mileage' => 9_999,
                    'mileage_unit' => 'km',
                    'occurred_at' => '2026-03-01',
                ]),
                $this->serviceItem([
                    'id' => (string) Str::uuid(),
                    'vehicle_id' => $otherVehicleId,
                    'title' => 'Wrong vehicle owner',
                    'mileage' => 5_000,
                    'mileage_unit' => 'km',
                    'occurred_at' => '2026-04-01',
                ]),
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'created' => 1,
            'updated' => 1,
            'ignored' => 2,
        ]);
    }

    public function test_sync_requires_valid_mobile_header(): void
    {
        $response = $this->postJson('/api/service-items/sync', [
            'service_items' => [],
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['mobile_id']);
    }

    public function test_sync_persists_updated_at_from_mobile_payload(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();
        $serviceItemId = (string) Str::uuid();
        $updatedAt = '2026-06-01T12:00:00Z';

        Vehicle::factory()->create([
            'external_id' => $vehicleId,
            'mobile_id' => $mobile->id,
        ]);

        $this->travelTo('2026-12-01T00:00:00Z');

        $response = $this->postJson('/api/service-items/sync', [
            'service_items' => [
                $this->serviceItem([
                    'id' => $serviceItemId,
                    'vehicle_id' => $vehicleId,
                    'title' => 'OIL CHANGE',
                    'mileage' => 42_350,
                    'mileage_unit' => 'km',
                    'occurred_at' => '2026-03-04',
                    'updated_at' => $updatedAt,
                ]),
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();

        $serviceItem = ServiceItem::query()->where('external_id', $serviceItemId)->first();

        $this->assertNotNull($serviceItem);
        $this->assertSame($updatedAt, $serviceItem->updated_at->toIso8601ZuluString());
    }

    public function test_sync_requires_updated_at(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();
        $serviceItemId = (string) Str::uuid();

        Vehicle::factory()->create([
            'external_id' => $vehicleId,
            'mobile_id' => $mobile->id,
        ]);

        $response = $this->postJson('/api/service-items/sync', [
            'service_items' => [
                [
                    'id' => $serviceItemId,
                    'vehicle_id' => $vehicleId,
                    'title' => 'OIL CHANGE',
                    'mileage' => 42_350,
                    'mileage_unit' => 'km',
                    'occurred_at' => '2026-03-04',
                ],
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['service_items.0.updated_at']);
    }
}
