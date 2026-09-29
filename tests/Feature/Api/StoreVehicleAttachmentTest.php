<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Mobile;
use App\Models\Vehicle;
use App\Models\VehicleAttachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

final class StoreVehicleAttachmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_upload_creates_attachment_for_owned_vehicle(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'mobile_id' => $mobile->id,
            'external_id' => (string) Str::uuid(),
        ]);
        $attachmentId = (string) Str::uuid();

        $response = $this->post(
            '/api/vehicles/'.$vehicle->external_id.'/attachments',
            [
                'id' => $attachmentId,
                'file' => UploadedFile::fake()->create('oil-change-receipt.pdf', 48, 'application/pdf'),
            ],
            [
                'X-Mobile-Id' => $mobile->id,
            ],
        );

        $response->assertCreated();
        $response->assertJson([
            'id' => $attachmentId,
            'vehicle_id' => $vehicle->external_id,
            'original_filename' => 'oil-change-receipt.pdf',
            'mime_type' => 'application/pdf',
        ]);
        $response->assertJsonStructure([
            'size',
            'created_at',
        ]);

        $this->assertDatabaseHas('vehicle_attachments', [
            'external_id' => $attachmentId,
            'vehicle_id' => $vehicle->id,
            'mobile_id' => $mobile->id,
            'original_filename' => 'oil-change-receipt.pdf',
            'mime_type' => 'application/pdf',
            'disk' => 'local',
        ]);

        $attachment = VehicleAttachment::query()->where('external_id', $attachmentId)->firstOrFail();
        Storage::disk('local')->assertExists($attachment->path);
    }

    public function test_upload_requires_mobile_header(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'mobile_id' => $mobile->id,
            'external_id' => (string) Str::uuid(),
        ]);

        $response = $this->post(
            '/api/vehicles/'.$vehicle->external_id.'/attachments',
            [
                'id' => (string) Str::uuid(),
                'file' => UploadedFile::fake()->create('receipt.pdf', 48, 'application/pdf'),
            ],
        );

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'mobile_id' => 'The X-Mobile-Id header is required.',
        ]);
        $response->assertJsonPath('message', 'The X-Mobile-Id header is required.');
    }

    public function test_upload_rejects_unknown_mobile_header(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'mobile_id' => $mobile->id,
            'external_id' => (string) Str::uuid(),
        ]);

        $response = $this->post(
            '/api/vehicles/'.$vehicle->external_id.'/attachments',
            [
                'id' => (string) Str::uuid(),
                'file' => UploadedFile::fake()->create('receipt.pdf', 48, 'application/pdf'),
            ],
            [
                'X-Mobile-Id' => (string) Str::uuid(),
            ],
        );

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'mobile_id' => 'The X-Mobile-Id header does not match a registered mobile device.',
        ]);
        $response->assertJsonPath('message', 'The X-Mobile-Id header does not match a registered mobile device.');
    }

    public function test_upload_rejects_invalid_attachment_id_format(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'mobile_id' => $mobile->id,
            'external_id' => (string) Str::uuid(),
        ]);

        $response = $this->post(
            '/api/vehicles/'.$vehicle->external_id.'/attachments',
            [
                'id' => 'not-a-uuid',
                'file' => UploadedFile::fake()->create('receipt.pdf', 48, 'application/pdf'),
            ],
            [
                'X-Mobile-Id' => $mobile->id,
            ],
        );

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'id' => 'The attachment id must be a valid UUID.',
        ]);
        $response->assertJsonPath('message', 'The attachment id must be a valid UUID.');
    }

    public function test_upload_requires_file(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'mobile_id' => $mobile->id,
            'external_id' => (string) Str::uuid(),
        ]);

        $response = $this->post(
            '/api/vehicles/'.$vehicle->external_id.'/attachments',
            [
                'id' => (string) Str::uuid(),
            ],
            [
                'X-Mobile-Id' => $mobile->id,
            ],
        );

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'file' => 'The attachment file is required.',
        ]);
        $response->assertJsonPath('message', 'The attachment file is required.');
    }

    public function test_upload_rejects_disallowed_file_type(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'mobile_id' => $mobile->id,
            'external_id' => (string) Str::uuid(),
        ]);

        $response = $this->post(
            '/api/vehicles/'.$vehicle->external_id.'/attachments',
            [
                'id' => (string) Str::uuid(),
                'file' => UploadedFile::fake()->create('notes.txt', 10, 'text/plain'),
            ],
            [
                'X-Mobile-Id' => $mobile->id,
            ],
        );

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'file' => 'The attachment file must be a JPEG, PNG, GIF, WebP image, or PDF.',
        ]);
        $response->assertJsonPath('message', 'The attachment file must be a JPEG, PNG, GIF, WebP image, or PDF.');
    }

    public function test_upload_rejects_oversized_file(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'mobile_id' => $mobile->id,
            'external_id' => (string) Str::uuid(),
        ]);

        $response = $this->post(
            '/api/vehicles/'.$vehicle->external_id.'/attachments',
            [
                'id' => (string) Str::uuid(),
                'file' => UploadedFile::fake()->create('large.pdf', 10241, 'application/pdf'),
            ],
            [
                'X-Mobile-Id' => $mobile->id,
            ],
        );

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'file' => 'The attachment file must not exceed 10 MB.',
        ]);
        $response->assertJsonPath('message', 'The attachment file must not exceed 10 MB.');
    }

    public function test_upload_rejects_duplicate_attachment_id(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'mobile_id' => $mobile->id,
            'external_id' => (string) Str::uuid(),
        ]);
        $attachmentId = (string) Str::uuid();

        VehicleAttachment::factory()->create([
            'external_id' => $attachmentId,
            'vehicle_id' => $vehicle->id,
            'mobile_id' => $mobile->id,
        ]);

        $response = $this->post(
            '/api/vehicles/'.$vehicle->external_id.'/attachments',
            [
                'id' => $attachmentId,
                'file' => UploadedFile::fake()->create('receipt.pdf', 48, 'application/pdf'),
            ],
            [
                'X-Mobile-Id' => $mobile->id,
            ],
        );

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'id' => 'An attachment with this id already exists.',
        ]);
        $response->assertJsonPath('message', 'An attachment with this id already exists.');
    }

    public function test_upload_returns_not_found_for_unknown_vehicle(): void
    {
        $mobile = Mobile::factory()->create();

        $response = $this->post(
            '/api/vehicles/'.(string) Str::uuid().'/attachments',
            [
                'id' => (string) Str::uuid(),
                'file' => UploadedFile::fake()->create('receipt.pdf', 48, 'application/pdf'),
            ],
            [
                'X-Mobile-Id' => $mobile->id,
            ],
        );

        $response->assertNotFound();
    }

    public function test_upload_forbidden_when_vehicle_owned_by_other_mobile(): void
    {
        $owner = Mobile::factory()->create();
        $other = Mobile::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'mobile_id' => $owner->id,
            'external_id' => (string) Str::uuid(),
        ]);

        $response = $this->post(
            '/api/vehicles/'.$vehicle->external_id.'/attachments',
            [
                'id' => (string) Str::uuid(),
                'file' => UploadedFile::fake()->create('receipt.pdf', 48, 'application/pdf'),
            ],
            [
                'X-Mobile-Id' => $other->id,
            ],
        );

        $response->assertForbidden();
        $response->assertJsonPath('message', 'This vehicle is not owned by the requesting mobile device.');
    }

    public function test_upload_forbidden_when_vehicle_is_unassigned(): void
    {
        $mobile = Mobile::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'mobile_id' => null,
            'external_id' => (string) Str::uuid(),
        ]);

        $response = $this->post(
            '/api/vehicles/'.$vehicle->external_id.'/attachments',
            [
                'id' => (string) Str::uuid(),
                'file' => UploadedFile::fake()->create('receipt.pdf', 48, 'application/pdf'),
            ],
            [
                'X-Mobile-Id' => $mobile->id,
            ],
        );

        $response->assertForbidden();
        $response->assertJsonPath('message', 'This vehicle is not owned by the requesting mobile device.');
    }
}
