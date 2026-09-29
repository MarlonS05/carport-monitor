<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MileageUnit;
use App\Models\ServiceItem;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceItem>
 */
final class ServiceItemFactory extends Factory
{
    protected $model = ServiceItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'external_id' => fake()->unique()->slug(3),
            'title' => strtoupper(fake()->randomElement([
                'Oil change',
                'Tire rotation',
                'Brake inspection',
                'Chain service',
                'Cabin air filter',
            ])),
            'description' => fake()->optional()->sentence(),
            'mileage' => fake()->numberBetween(5_000, 120_000),
            'mileage_unit' => MileageUnit::Kilometers,
            'occurred_at' => fake()->dateTimeBetween('-2 years'),
        ];
    }
}
