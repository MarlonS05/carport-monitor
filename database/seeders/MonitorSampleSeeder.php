<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\MileageUnit;
use App\Models\Mobile;
use App\Models\ServiceItem;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleAttachment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

final class MonitorSampleSeeder extends Seeder
{
    public function run(): void
    {
        $mobile = Mobile::query()->create();

        $alex = User::query()->where('email', 'alex@carport.local')->first();
        $jordan = User::query()->where('email', 'jordan@carport.local')->first();

        if ($alex !== null && $jordan !== null) {
            $mobile->users()->sync([$alex->id, $jordan->id]);
        }

        $mazda = Vehicle::query()->create([
            'external_id' => 'mazda-cx5-2019',
            'mobile_id' => $mobile->id,
            'name' => '2019 Mazda CX-5',
            'mileage' => 42_350,
            'mileage_unit' => MileageUnit::Kilometers,
            'description' => "Soul Red Crystal Metallic\nPremium trim package\nPurchased March 2019 from local dealer",
            'user_manual_url' => null,
            'service_manual_url' => null,
        ]);

        $this->createServiceItem($mazda, $mobile->id, [
            'external_id' => 'oil-change-2026-03',
            'title' => 'OIL CHANGE',
            'description' => 'Full synthetic 0W-20, filter replaced.',
            'mileage' => 42_350,
            'occurred_at' => '2026-03-04',
        ]);

        $this->createServiceItem($mazda, $mobile->id, [
            'external_id' => 'tire-rotation-2026-03',
            'title' => 'TIRE ROTATION',
            'description' => 'Rotated all four tires, pressures checked.',
            'mileage' => 42_350,
            'occurred_at' => '2026-03-04',
        ]);

        $this->createServiceItem($mazda, $mobile->id, [
            'external_id' => 'brake-inspection-2026-01',
            'title' => 'BRAKE INSPECTION',
            'description' => 'Front pads at 60%, rears at 75%. No service needed.',
            'mileage' => 40_100,
            'occurred_at' => '2026-01-12',
        ]);

        $this->createServiceItem($mazda, $mobile->id, [
            'external_id' => 'air-filter-2025-11',
            'title' => 'CABIN AIR FILTER',
            'description' => null,
            'mileage' => 38_500,
            'occurred_at' => '2025-11-08',
        ]);

        $this->createAttachment($mazda, $mobile->id, [
            'external_id' => 'oil-change-receipt-2026-03',
            'original_filename' => 'oil-change-receipt.pdf',
            'mime_type' => 'application/pdf',
            'contents' => '%PDF-1.4 oil change receipt',
        ]);

        $this->createAttachment($mazda, $mobile->id, [
            'external_id' => 'tire-rotation-photo-2026-03',
            'original_filename' => 'tire-rotation.jpg',
            'mime_type' => 'image/jpeg',
            'contents' => 'jpeg tire rotation photo',
        ]);

        $honda = Vehicle::query()->create([
            'external_id' => 'honda-cb500f-2016',
            'mobile_id' => $mobile->id,
            'name' => '2016 Honda CB500F',
            'mileage' => 18_920,
            'mileage_unit' => MileageUnit::Kilometers,
            'description' => 'Matte black · Commuter bike',
        ]);

        $this->createServiceItem($honda, $mobile->id, [
            'external_id' => 'chain-service-2026-02',
            'title' => 'CHAIN SERVICE',
            'description' => 'Cleaned, lubricated, and adjusted chain tension.',
            'mileage' => 18_920,
            'occurred_at' => '2026-02-18',
        ]);

        $this->createServiceItem($honda, $mobile->id, [
            'external_id' => 'oil-change-2025-12',
            'title' => 'OIL CHANGE',
            'description' => '10W-40 motorcycle oil, new crush washer.',
            'mileage' => 17_600,
            'occurred_at' => '2025-12-02',
        ]);

        Vehicle::query()->create([
            'external_id' => 'unassigned-demo-vehicle',
            'name' => 'Unassigned Demo Vehicle',
            'mileage' => 1_000,
            'mileage_unit' => MileageUnit::Kilometers,
            'description' => 'Visible to all authenticated users without mobile permissions.',
        ]);
    }

    /**
     * @param  array{
     *     external_id: string,
     *     title: string,
     *     description: ?string,
     *     mileage: int,
     *     occurred_at: string,
     * }  $attributes
     */
    private function createServiceItem(Vehicle $vehicle, string $mobileId, array $attributes): ServiceItem
    {
        return ServiceItem::query()->create([
            'vehicle_id' => $vehicle->id,
            'mobile_id' => $mobileId,
            'external_id' => $attributes['external_id'],
            'title' => $attributes['title'],
            'description' => $attributes['description'],
            'mileage' => $attributes['mileage'],
            'mileage_unit' => MileageUnit::Kilometers,
            'occurred_at' => $attributes['occurred_at'],
        ]);
    }

    /**
     * @param  array{
     *     external_id: string,
     *     original_filename: string,
     *     mime_type: string,
     *     contents: string,
     * }  $attributes
     */
    private function createAttachment(Vehicle $vehicle, string $mobileId, array $attributes): VehicleAttachment
    {
        $extension = match ($attributes['mime_type']) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
            default => 'bin',
        };

        $path = 'vehicle-attachments/'.$vehicle->external_id.'/'.$attributes['external_id'].'.'.$extension;
        $contents = $attributes['contents'];

        Storage::disk('local')->put($path, $contents);

        return VehicleAttachment::query()->create([
            'external_id' => $attributes['external_id'],
            'vehicle_id' => $vehicle->id,
            'mobile_id' => $mobileId,
            'original_filename' => $attributes['original_filename'],
            'mime_type' => $attributes['mime_type'],
            'size' => strlen($contents),
            'disk' => 'local',
            'path' => $path,
        ]);
    }
}
