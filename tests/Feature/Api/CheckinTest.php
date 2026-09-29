<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Mobile;
use App\Models\ServiceItem;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleAttachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CheckinTest extends TestCase
{
    use RefreshDatabase;

    private const string PORTAL_UPDATED_AT = '2026-07-08T10:15:30Z';

    public function test_checkin_returns_users_and_sync_status_when_mobile_matches_portal(): void
    {
        $mobile = Mobile::factory()->create();
        $alex = User::factory()->create(['name' => 'Alex Morgan']);
        $jordan = User::factory()->create(['name' => 'Jordan Lee']);

        Vehicle::factory()->create([
            'mobile_id' => $mobile->id,
            'updated_at' => self::PORTAL_UPDATED_AT,
        ]);

        $response = $this->getJson('/api/checkin/'.self::PORTAL_UPDATED_AT);

        $response->assertOk();
        $response->assertJson([
            'users' => [
                (string) $alex->id => 'Alex Morgan',
                (string) $jordan->id => 'Jordan Lee',
            ],
            'isDatabaseSync' => true,
        ]);
    }

    public function test_checkin_reports_synced_when_mobile_is_behind_portal(): void
    {
        Mobile::factory()->create();

        Vehicle::factory()->create([
            'updated_at' => '2026-07-08T10:15:30Z',
        ]);

        $response = $this->getJson('/api/checkin/2026-07-08T10:15:29Z');

        $response->assertOk();
        $response->assertJsonPath('isDatabaseSync', true);
    }

    public function test_checkin_reports_not_synced_when_mobile_is_ahead_of_portal(): void
    {
        Mobile::factory()->create();

        Vehicle::factory()->create([
            'updated_at' => '2026-07-08T10:15:30Z',
        ]);

        $response = $this->getJson('/api/checkin/2026-07-08T10:15:31Z');

        $response->assertOk();
        $response->assertJsonPath('isDatabaseSync', false);
    }

    public function test_checkin_uses_max_updated_at_across_vehicles_and_service_items(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'mobile_id' => $mobile->id,
            'updated_at' => '2026-07-08T10:15:29Z',
        ]);

        ServiceItem::factory()->create([
            'vehicle_id' => $vehicle->id,
            'mobile_id' => $mobile->id,
            'updated_at' => '2026-07-08T10:15:30Z',
        ]);

        $response = $this->getJson('/api/checkin/2026-07-08T10:15:30Z');

        $response->assertOk();
        $response->assertJsonPath('isDatabaseSync', true);

        $response = $this->getJson('/api/checkin/2026-07-08T10:15:31Z');

        $response->assertOk();
        $response->assertJsonPath('isDatabaseSync', false);
    }

    public function test_checkin_uses_max_updated_at_across_vehicles_service_items_and_attachments(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'mobile_id' => $mobile->id,
            'updated_at' => '2026-07-08T10:15:29Z',
        ]);

        VehicleAttachment::factory()->create([
            'vehicle_id' => $vehicle->id,
            'mobile_id' => $mobile->id,
            'updated_at' => '2026-07-08T10:15:30Z',
        ]);

        $response = $this->getJson('/api/checkin/2026-07-08T10:15:30Z');

        $response->assertOk();
        $response->assertJsonPath('isDatabaseSync', true);

        $response = $this->getJson('/api/checkin/2026-07-08T10:15:31Z');

        $response->assertOk();
        $response->assertJsonPath('isDatabaseSync', false);
    }

    public function test_checkin_reports_not_synced_when_portal_has_no_records(): void
    {
        User::factory()->create(['name' => 'Sam Rivera']);

        $response = $this->getJson('/api/checkin/'.self::PORTAL_UPDATED_AT);

        $response->assertOk();
        $response->assertJsonPath('isDatabaseSync', false);
        $response->assertJsonCount(1, 'users');
    }

    public function test_checkin_returns_empty_users_when_none_exist(): void
    {
        $response = $this->getJson('/api/checkin/'.self::PORTAL_UPDATED_AT);

        $response->assertOk();
        $response->assertJson([
            'users' => [],
            'isDatabaseSync' => false,
        ]);
    }

    public function test_checkin_accepts_url_encoded_datetime(): void
    {
        Vehicle::factory()->create([
            'updated_at' => '2026-07-08T10:15:30Z',
        ]);

        $response = $this->getJson('/api/checkin/2026-07-08T10%3A15%3A30Z');

        $response->assertOk();
        $response->assertJsonPath('isDatabaseSync', true);
    }

    public function test_checkin_compares_timestamps_to_the_second(): void
    {
        Vehicle::factory()->create([
            'updated_at' => '2026-07-08T10:15:30.999Z',
        ]);

        $response = $this->getJson('/api/checkin/2026-07-08T10:15:30Z');

        $response->assertOk();
        $response->assertJsonPath('isDatabaseSync', true);
    }

    public function test_checkin_rejects_invalid_datetime(): void
    {
        $response = $this->getJson('/api/checkin/not-a-datetime');

        $response->assertNotFound();
    }
}
