<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Mobile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mobile>
 */
final class MobileFactory extends Factory
{
    protected $model = Mobile::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [];
    }
}
