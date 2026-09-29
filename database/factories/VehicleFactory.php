<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MileageUnit;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
final class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'external_id' => fake()->unique()->slug(3),
            'name' => fake()->numberBetween(2010, (int) date('Y')).' '.fake()->randomElement(['Mazda CX-5', 'Honda CB500F', 'Toyota RAV4', 'BMW 320i']),
            'mileage' => fake()->numberBetween(5_000, 120_000),
            'mileage_unit' => MileageUnit::Kilometers,
            'description' => fake()->optional()->paragraph(),
            'maintenance_schedule_image' => null,
            'user_manual_url' => fake()->optional()->url(),
            'service_manual_url' => fake()->optional()->url(),
        ];
    }
}
