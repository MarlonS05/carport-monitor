<?php

declare(strict_types=1);

namespace Tests\Feature\Monitor;

use App\Models\Mobile;
use App\Models\Vehicle;
use App\Models\VehicleAttachment;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class VehicleAttachmentDownloadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->seed(TestDatabaseSeeder::class);
    }

    public function test_authorized_user_can_download_attachment(): void
    {
        $attachment = VehicleAttachment::query()
            ->where('external_id', 'oil-change-receipt-2026-03')
            ->firstOrFail();

        Storage::disk('local')->put($attachment->path, '%PDF-1.4 oil change receipt');

        $this->loginAsSeededUser('alex@carport.local');

        $response = $this->get(route('monitor.vehicles.attachments.download', [
            'vehicle' => 'mazda-cx5-2019',
            'attachment' => $attachment->external_id,
        ]));

        $response->assertOk();
        $response->assertDownload('oil-change-receipt.pdf');
        $this->assertSame('%PDF-1.4 oil change receipt', $response->streamedContent());
    }

    public function test_unauthorized_user_cannot_download_attachment(): void
    {
        $this->loginAsSeededUser('sam@carport.local');

        $response = $this->get(route('monitor.vehicles.attachments.download', [
            'vehicle' => 'mazda-cx5-2019',
            'attachment' => 'oil-change-receipt-2026-03',
        ]));

        $response->assertNotFound();
    }

    public function test_download_returns_not_found_when_attachment_belongs_to_different_vehicle(): void
    {
        $mobile = Mobile::query()->firstOrFail();
        $otherVehicle = Vehicle::query()->where('external_id', 'honda-cb500f-2016')->firstOrFail();

        $attachment = VehicleAttachment::factory()->create([
            'external_id' => 'cross-vehicle-attachment',
            'vehicle_id' => $otherVehicle->id,
            'mobile_id' => $mobile->id,
            'path' => 'vehicle-attachments/'.$otherVehicle->external_id.'/cross-vehicle-attachment.pdf',
        ]);

        Storage::disk('local')->put($attachment->path, 'other vehicle file');

        $this->loginAsSeededUser('alex@carport.local');

        $response = $this->get(route('monitor.vehicles.attachments.download', [
            'vehicle' => 'mazda-cx5-2019',
            'attachment' => $attachment->external_id,
        ]));

        $response->assertNotFound();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('monitor.vehicles.attachments.download', [
            'vehicle' => 'mazda-cx5-2019',
            'attachment' => 'oil-change-receipt-2026-03',
        ]));

        $response->assertRedirect(route('login'));
    }
}
