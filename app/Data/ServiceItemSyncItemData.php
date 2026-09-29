<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\MileageUnit;

final readonly class ServiceItemSyncItemData
{
    public function __construct(
        public string $id,
        public string $vehicleId,
        public string $title,
        public ?string $description,
        public int $mileage,
        public MileageUnit $mileageUnit,
        public string $occurredAt,
        public string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            id: $payload['id'],
            vehicleId: $payload['vehicle_id'],
            title: $payload['title'],
            description: $payload['description'] ?? null,
            mileage: (int) $payload['mileage'],
            mileageUnit: MileageUnit::from($payload['mileage_unit']),
            occurredAt: $payload['occurred_at'],
            updatedAt: $payload['updated_at'],
        );
    }
}
