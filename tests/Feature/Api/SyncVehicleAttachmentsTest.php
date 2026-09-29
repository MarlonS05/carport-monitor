<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Mobile;
use App\Models\Vehicle;
use App\Models\VehicleAttachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

final class SyncVehicleAttachmentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_sync_deletes_attachments_not_in_list(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicle = Vehicle::factory()->create(['mobile_id' => $mobile->id]);
        $keepId = (string) Str::uuid();
        $removeId = (string) Str::uuid();

        $kept = VehicleAttachment::factory()->create([
            'external_id' => $keepId,
            'vehicle_id' => $vehicle->id,
            'mobile_id' => $mobile->id,
        ]);
        $removed = VehicleAttachment::factory()->create([
            'external_id' => $removeId,
            'vehicle_id' => $vehicle->id,
            'mobile_id' => $mobile->id,
        ]);

        Storage::disk('local')->put($kept->path, 'kept');
        Storage::disk('local')->put($removed->path, 'removed');

        $response = $this->postJson('/api/attachments/sync', [
            'attachment_ids' => [$keepId],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'deleted' => 1,
            'attachment_ids' => [$removeId],
        ]);

        $this->assertDatabaseHas('vehicle_attachments', [
            'external_id' => $keepId,
        ]);
        $this->assertDatabaseMissing('vehicle_attachments', [
            'external_id' => $removeId,
        ]);
        Storage::disk('local')->assertExists($kept->path);
        Storage::disk('local')->assertMissing($removed->path);
    }

    public function test_sync_with_empty_array_deletes_all_mobile_attachments(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicle = Vehicle::factory()->create(['mobile_id' => $mobile->id]);
        $attachment = VehicleAttachment::factory()->create([
            'vehicle_id' => $vehicle->id,
            'mobile_id' => $mobile->id,
        ]);

        Storage::disk('local')->put($attachment->path, 'content');

        $response = $this->postJson('/api/attachments/sync', [
            'attachment_ids' => [],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'deleted' => 1,
            'attachment_ids' => [$attachment->external_id],
        ]);

        $this->assertDatabaseMissing('vehicle_attachments', [
            'external_id' => $attachment->external_id,
        ]);
        Storage::disk('local')->assertMissing($attachment->path);
    }

    public function test_sync_does_not_delete_attachments_from_other_mobiles(): void
    {
        $mobile = Mobile::factory()->create();
        $otherMobile = Mobile::factory()->create();
        $vehicle = Vehicle::factory()->create(['mobile_id' => $mobile->id]);
        $otherVehicle = Vehicle::factory()->create(['mobile_id' => $otherMobile->id]);

        $ownAttachment = VehicleAttachment::factory()->create([
            'vehicle_id' => $vehicle->id,
            'mobile_id' => $mobile->id,
        ]);
        $otherAttachment = VehicleAttachment::factory()->create([
            'vehicle_id' => $otherVehicle->id,
            'mobile_id' => $otherMobile->id,
        ]);

        Storage::disk('local')->put($ownAttachment->path, 'own');
        Storage::disk('local')->put($otherAttachment->path, 'other');

        $response = $this->postJson('/api/attachments/sync', [
            'attachment_ids' => [],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'deleted' => 1,
            'attachment_ids' => [$ownAttachment->external_id],
        ]);

        $this->assertDatabaseMissing('vehicle_attachments', [
            'external_id' => $ownAttachment->external_id,
        ]);
        $this->assertDatabaseHas('vehicle_attachments', [
            'external_id' => $otherAttachment->external_id,
        ]);
        Storage::disk('local')->assertMissing($ownAttachment->path);
        Storage::disk('local')->assertExists($otherAttachment->path);
    }

    public function test_sync_touches_affected_vehicles(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'mobile_id' => $mobile->id,
            'updated_at' => now()->subDay(),
        ]);
        $attachment = VehicleAttachment::factory()->create([
            'vehicle_id' => $vehicle->id,
            'mobile_id' => $mobile->id,
        ]);

        Storage::disk('local')->put($attachment->path, 'content');

        $this->postJson('/api/attachments/sync', [
            'attachment_ids' => [],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ])->assertOk();

        $vehicle->refresh();

        $this->assertTrue($vehicle->updated_at->greaterThan(now()->subMinute()));
    }

    public function test_sync_requires_valid_mobile_id_header(): void
    {
        $response = $this->postJson('/api/attachments/sync', [
            'attachment_ids' => [],
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['mobile_id']);
    }

    public function test_sync_rejects_invalid_attachment_id_format(): void
    {
        $mobile = Mobile::factory()->create();

        $response = $this->postJson('/api/attachments/sync', [
            'attachment_ids' => ['not-a-uuid'],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['attachment_ids.0']);
    }
}
