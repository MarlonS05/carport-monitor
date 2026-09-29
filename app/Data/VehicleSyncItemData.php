<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\MileageUnit;

final readonly class VehicleSyncItemData
{
    public function __construct(
        public string $id,
        public string $name,
        public int $mileage,
        public MileageUnit $mileageUnit,
        public ?string $description,
        public ?string $userManualUrl,
        public ?string $serviceManualUrl,
        public ?string $maintenanceScheduleImage,
        public bool $maintenanceScheduleImageProvided,
        public string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            id: $payload['id'],
            name: $payload['name'],
            mileage: (int) $payload['mileage'],
            mileageUnit: MileageUnit::from($payload['mileage_unit']),
            description: $payload['description'] ?? null,
            userManualUrl: $payload['user_manual_url'] ?? null,
            serviceManualUrl: $payload['service_manual_url'] ?? null,
            maintenanceScheduleImage: $payload['maintenance_schedule_image'] ?? null,
            maintenanceScheduleImageProvided: array_key_exists('maintenance_schedule_image', $payload),
            updatedAt: $payload['updated_at'],
        );
    }
}
