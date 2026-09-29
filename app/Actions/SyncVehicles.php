<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\SyncItemResult;
use App\Data\SyncSummary;
use App\Data\VehicleSyncItemData;
use App\Enums\SyncItemStatus;
use App\Models\Mobile;
use App\Models\Vehicle;

final class SyncVehicles
{
    /**
     * @param  list<VehicleSyncItemData>  $vehicles
     */
    public function handle(Mobile $mobile, array $vehicles): SyncSummary
    {
        $created = 0;
        $updated = 0;
        $ignored = 0;
        $items = [];

        foreach ($vehicles as $vehicleData) {
            $existing = Vehicle::query()
                ->where('external_id', $vehicleData->id)
                ->first();

            if ($existing !== null) {
                if ($existing->mobile_id !== $mobile->id) {
                    $ignored++;
                    $items[] = new SyncItemResult($vehicleData->id, SyncItemStatus::Ignored);

                    continue;
                }

                $existing->update($this->attributesFromData($vehicleData));
                $updated++;
                $items[] = new SyncItemResult($vehicleData->id, SyncItemStatus::Updated);

                continue;
            }

            Vehicle::query()->create([
                'external_id' => $vehicleData->id,
                'mobile_id' => $mobile->id,
                ...$this->attributesFromData($vehicleData),
            ]);
            $created++;
            $items[] = new SyncItemResult($vehicleData->id, SyncItemStatus::Created);
        }

        return new SyncSummary($created, $updated, $ignored, $items);
    }

    /**
     * @return array<string, mixed>
     */
    private function attributesFromData(VehicleSyncItemData $vehicleData): array
    {
        $attributes = [
            'name' => $vehicleData->name,
            'mileage' => $vehicleData->mileage,
            'mileage_unit' => $vehicleData->mileageUnit,
            'description' => $vehicleData->description,
            'user_manual_url' => $vehicleData->userManualUrl,
            'service_manual_url' => $vehicleData->serviceManualUrl,
            'updated_at' => $vehicleData->updatedAt,
        ];

        if ($vehicleData->maintenanceScheduleImageProvided) {
            $attributes['maintenance_schedule_image'] = $vehicleData->maintenanceScheduleImage;
        }

        return $attributes;
    }
}
