<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\ServiceItemSyncItemData;
use App\Data\SyncItemResult;
use App\Data\SyncSummary;
use App\Enums\SyncItemStatus;
use App\Models\Mobile;
use App\Models\ServiceItem;
use App\Models\Vehicle;

final class SyncServiceItems
{
    /**
     * @param  list<ServiceItemSyncItemData>  $serviceItems
     */
    public function handle(Mobile $mobile, array $serviceItems): SyncSummary
    {
        $created = 0;
        $updated = 0;
        $ignored = 0;
        $items = [];

        foreach ($serviceItems as $serviceItemData) {
            $existing = ServiceItem::query()
                ->where('external_id', $serviceItemData->id)
                ->first();

            if ($existing !== null) {
                if ($existing->mobile_id !== $mobile->id) {
                    $ignored++;
                    $items[] = new SyncItemResult($serviceItemData->id, SyncItemStatus::Ignored);

                    continue;
                }

                $vehicle = $this->resolveVehicle($mobile, $serviceItemData->vehicleId);

                if ($vehicle === null) {
                    $ignored++;
                    $items[] = new SyncItemResult($serviceItemData->id, SyncItemStatus::Ignored);

                    continue;
                }

                $existing->update([
                    'vehicle_id' => $vehicle->id,
                    ...$this->attributesFromData($serviceItemData),
                ]);
                $updated++;
                $items[] = new SyncItemResult($serviceItemData->id, SyncItemStatus::Updated);

                continue;
            }

            $vehicle = $this->resolveVehicle($mobile, $serviceItemData->vehicleId);

            if ($vehicle === null) {
                $ignored++;
                $items[] = new SyncItemResult($serviceItemData->id, SyncItemStatus::Ignored);

                continue;
            }

            ServiceItem::query()->create([
                'external_id' => $serviceItemData->id,
                'vehicle_id' => $vehicle->id,
                'mobile_id' => $mobile->id,
                ...$this->attributesFromData($serviceItemData),
            ]);
            $created++;
            $items[] = new SyncItemResult($serviceItemData->id, SyncItemStatus::Created);
        }

        return new SyncSummary($created, $updated, $ignored, $items);
    }

    private function resolveVehicle(Mobile $mobile, string $vehicleExternalId): ?Vehicle
    {
        return Vehicle::query()
            ->where('external_id', $vehicleExternalId)
            ->where('mobile_id', $mobile->id)
            ->first();
    }

    /**
     * @return array<string, mixed>
     */
    private function attributesFromData(ServiceItemSyncItemData $serviceItemData): array
    {
        return [
            'title' => $serviceItemData->title,
            'description' => $serviceItemData->description,
            'mileage' => $serviceItemData->mileage,
            'mileage_unit' => $serviceItemData->mileageUnit,
            'occurred_at' => $serviceItemData->occurredAt,
            'updated_at' => $serviceItemData->updatedAt,
        ];
    }
}
