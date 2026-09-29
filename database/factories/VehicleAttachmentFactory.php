<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Mobile;
use App\Models\Vehicle;
use App\Models\VehicleAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VehicleAttachment>
 */
final class VehicleAttachmentFactory extends Factory
{
    protected $model = VehicleAttachment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $externalId = (string) Str::uuid();

        return [
            'external_id' => $externalId,
            'vehicle_id' => Vehicle::factory(),
            'mobile_id' => Mobile::factory(),
            'original_filename' => 'receipt.pdf',
            'mime_type' => 'application/pdf',
            'size' => fake()->numberBetween(1_000, 500_000),
            'disk' => 'local',
            'path' => 'vehicle-attachments/'.fake()->uuid().'/'.$externalId.'.pdf',
        ];
    }
}
