<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Enums\MileageUnit;
use App\Models\Mobile;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class SyncVehiclesTest extends TestCase
{
    use RefreshDatabase;

    private const string PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    private const string UPDATED_AT = '2026-07-08T10:15:30Z';

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function vehicle(array $payload): array
    {
        return array_merge(['updated_at' => self::UPDATED_AT], $payload);
    }

    public function test_sync_creates_new_vehicles_for_mobile(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();

        $response = $this->postJson('/api/vehicles/sync', [
            'vehicles' => [
                $this->vehicle([
                    'id' => $vehicleId,
                    'name' => '2019 Mazda CX-5',
                    'mileage' => 42_350,
                    'mileage_unit' => 'km',
                    'description' => 'Soul Red',
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
                ['id' => $vehicleId, 'status' => 'created'],
            ],
        ]);

        $this->assertDatabaseHas('vehicles', [
            'external_id' => $vehicleId,
            'mobile_id' => $mobile->id,
            'name' => '2019 Mazda CX-5',
            'mileage' => 42_350,
        ]);
    }

    public function test_sync_updates_vehicle_when_mobile_matches(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();

        Vehicle::factory()->create([
            'external_id' => $vehicleId,
            'mobile_id' => $mobile->id,
            'name' => 'Old name',
            'mileage' => 10_000,
        ]);

        $response = $this->postJson('/api/vehicles/sync', [
            'vehicles' => [
                $this->vehicle([
                    'id' => $vehicleId,
                    'name' => 'Updated name',
                    'mileage' => 20_000,
                    'mileage_unit' => 'mi',
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
            'items' => [
                ['id' => $vehicleId, 'status' => 'updated'],
            ],
        ]);

        $this->assertDatabaseHas('vehicles', [
            'external_id' => $vehicleId,
            'mobile_id' => $mobile->id,
            'name' => 'Updated name',
            'mileage' => 20_000,
            'mileage_unit' => MileageUnit::Miles->value,
        ]);
        $this->assertDatabaseCount('vehicles', 1);
    }

    public function test_sync_ignores_vehicle_owned_by_different_mobile(): void
    {
        $owner = Mobile::factory()->create();
        $other = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();

        Vehicle::factory()->create([
            'external_id' => $vehicleId,
            'mobile_id' => $owner->id,
            'name' => 'Owned by other',
            'mileage' => 10_000,
        ]);

        $response = $this->postJson('/api/vehicles/sync', [
            'vehicles' => [
                $this->vehicle([
                    'id' => $vehicleId,
                    'name' => 'Attempted overwrite',
                    'mileage' => 99_999,
                    'mileage_unit' => 'km',
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
            'items' => [
                ['id' => $vehicleId, 'status' => 'ignored'],
            ],
        ]);

        $this->assertDatabaseHas('vehicles', [
            'external_id' => $vehicleId,
            'mobile_id' => $owner->id,
            'name' => 'Owned by other',
            'mileage' => 10_000,
        ]);
    }

    public function test_sync_handles_bulk_array_with_mixed_outcomes(): void
    {
        $mobile = Mobile::factory()->create();
        $other = Mobile::factory()->create();

        $createId = (string) Str::uuid();
        $updateId = (string) Str::uuid();
        $ignoreId = (string) Str::uuid();

        Vehicle::factory()->create([
            'external_id' => $updateId,
            'mobile_id' => $mobile->id,
            'name' => 'Before update',
            'mileage' => 1_000,
        ]);

        Vehicle::factory()->create([
            'external_id' => $ignoreId,
            'mobile_id' => $other->id,
            'name' => 'Other mobile vehicle',
            'mileage' => 2_000,
        ]);

        $response = $this->postJson('/api/vehicles/sync', [
            'vehicles' => [
                $this->vehicle([
                    'id' => $createId,
                    'name' => 'New vehicle',
                    'mileage' => 3_000,
                    'mileage_unit' => 'km',
                ]),
                $this->vehicle([
                    'id' => $updateId,
                    'name' => 'After update',
                    'mileage' => 4_000,
                    'mileage_unit' => 'km',
                ]),
                $this->vehicle([
                    'id' => $ignoreId,
                    'name' => 'Should not apply',
                    'mileage' => 9_999,
                    'mileage_unit' => 'km',
                ]),
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'created' => 1,
            'updated' => 1,
            'ignored' => 1,
        ]);

        $this->assertDatabaseHas('vehicles', ['external_id' => $createId, 'name' => 'New vehicle']);
        $this->assertDatabaseHas('vehicles', ['external_id' => $updateId, 'name' => 'After update']);
        $this->assertDatabaseHas('vehicles', ['external_id' => $ignoreId, 'name' => 'Other mobile vehicle']);
    }

    public function test_sync_requires_valid_mobile_header(): void
    {
        $response = $this->postJson('/api/vehicles/sync', [
            'vehicles' => [],
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['mobile_id']);
    }

    public function test_sync_requires_vehicles_array(): void
    {
        $mobile = Mobile::factory()->create();

        $response = $this->postJson('/api/vehicles/sync', [], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['vehicles']);
    }

    public function test_sync_stores_maintenance_schedule_image_on_create(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();

        $response = $this->postJson('/api/vehicles/sync', [
            'vehicles' => [
                $this->vehicle([
                    'id' => $vehicleId,
                    'name' => '2019 Mazda CX-5',
                    'mileage' => 42_350,
                    'mileage_unit' => 'km',
                    'maintenance_schedule_image' => self::PNG_BASE64,
                ]),
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('vehicles', [
            'external_id' => $vehicleId,
            'maintenance_schedule_image' => self::PNG_BASE64,
        ]);
    }

    public function test_sync_update_without_image_preserves_existing_image(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();

        Vehicle::factory()->create([
            'external_id' => $vehicleId,
            'mobile_id' => $mobile->id,
            'name' => 'Before update',
            'maintenance_schedule_image' => self::PNG_BASE64,
        ]);

        $response = $this->postJson('/api/vehicles/sync', [
            'vehicles' => [
                $this->vehicle([
                    'id' => $vehicleId,
                    'name' => 'After update',
                    'mileage' => 20_000,
                    'mileage_unit' => 'km',
                ]),
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('vehicles', [
            'external_id' => $vehicleId,
            'name' => 'After update',
            'maintenance_schedule_image' => self::PNG_BASE64,
        ]);
    }

    public function test_sync_update_with_null_clears_existing_image(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();

        Vehicle::factory()->create([
            'external_id' => $vehicleId,
            'mobile_id' => $mobile->id,
            'maintenance_schedule_image' => self::PNG_BASE64,
        ]);

        $response = $this->postJson('/api/vehicles/sync', [
            'vehicles' => [
                $this->vehicle([
                    'id' => $vehicleId,
                    'name' => '2019 Mazda CX-5',
                    'mileage' => 42_350,
                    'mileage_unit' => 'km',
                    'maintenance_schedule_image' => null,
                ]),
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();

        $vehicle = Vehicle::query()->where('external_id', $vehicleId)->first();

        $this->assertNotNull($vehicle);
        $this->assertNull($vehicle->maintenance_schedule_image);
    }

    public function test_sync_rejects_invalid_base64_image(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();

        $response = $this->postJson('/api/vehicles/sync', [
            'vehicles' => [
                $this->vehicle([
                    'id' => $vehicleId,
                    'name' => '2019 Mazda CX-5',
                    'mileage' => 42_350,
                    'mileage_unit' => 'km',
                    'maintenance_schedule_image' => 'not-valid-base64!!!',
                ]),
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['vehicles.0.maintenance_schedule_image']);
    }

    public function test_sync_accepts_non_url_manual_strings(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();

        $response = $this->postJson('/api/vehicles/sync', [
            'vehicles' => [
                $this->vehicle([
                    'id' => $vehicleId,
                    'name' => '2019 Mazda CX-5',
                    'mileage' => 42_350,
                    'mileage_unit' => 'km',
                    'user_manual_url' => 'My local manual.pdf',
                    'service_manual_url' => 'https://example.com/manual',
                ]),
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('vehicles', [
            'external_id' => $vehicleId,
            'user_manual_url' => 'My local manual.pdf',
            'service_manual_url' => 'https://example.com/manual',
        ]);
    }

    public function test_sync_rejects_javascript_manual_url(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();

        $response = $this->postJson('/api/vehicles/sync', [
            'vehicles' => [
                $this->vehicle([
                    'id' => $vehicleId,
                    'name' => '2019 Mazda CX-5',
                    'mileage' => 42_350,
                    'mileage_unit' => 'km',
                    'user_manual_url' => 'javascript:alert(1)',
                ]),
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['vehicles.0.user_manual_url']);
    }

    public function test_sync_rejects_data_manual_url(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();

        $response = $this->postJson('/api/vehicles/sync', [
            'vehicles' => [
                $this->vehicle([
                    'id' => $vehicleId,
                    'name' => '2019 Mazda CX-5',
                    'mileage' => 42_350,
                    'mileage_unit' => 'km',
                    'service_manual_url' => 'data:text/html,<script>alert(1)</script>',
                ]),
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['vehicles.0.service_manual_url']);
    }

    public function test_sync_rejects_non_string_manual_url(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();

        $response = $this->postJson('/api/vehicles/sync', [
            'vehicles' => [
                array_merge($this->vehicle([
                    'id' => $vehicleId,
                    'name' => '2019 Mazda CX-5',
                    'mileage' => 42_350,
                    'mileage_unit' => 'km',
                ]), ['user_manual_url' => 123]),
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['vehicles.0.user_manual_url']);
    }

    public function test_sync_persists_updated_at_from_mobile_payload(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();
        $updatedAt = '2026-06-01T12:00:00Z';

        $this->travelTo('2026-12-01T00:00:00Z');

        $response = $this->postJson('/api/vehicles/sync', [
            'vehicles' => [
                $this->vehicle([
                    'id' => $vehicleId,
                    'name' => '2019 Mazda CX-5',
                    'mileage' => 42_350,
                    'mileage_unit' => 'km',
                    'updated_at' => $updatedAt,
                ]),
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();

        $vehicle = Vehicle::query()->where('external_id', $vehicleId)->first();

        $this->assertNotNull($vehicle);
        $this->assertSame($updatedAt, $vehicle->updated_at->toIso8601ZuluString());
    }

    public function test_sync_requires_updated_at(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicleId = (string) Str::uuid();

        $response = $this->postJson('/api/vehicles/sync', [
            'vehicles' => [
                [
                    'id' => $vehicleId,
                    'name' => '2019 Mazda CX-5',
                    'mileage' => 42_350,
                    'mileage_unit' => 'km',
                ],
            ],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['vehicles.0.updated_at']);
    }
}
