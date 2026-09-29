<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\ProductionUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductionUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_seeder_creates_expected_users(): void
    {
        $this->seed(ProductionUserSeeder::class);

        $this->assertSame(3, User::query()->count());

        $this->assertNotNull(User::query()->where('name', 'Pete')->first());
        $this->assertNotNull(User::query()->where('name', 'Garfield')->first());
        $this->assertNotNull(User::query()->where('name', 'Amsterdam')->first());
    }

    public function test_production_seeder_is_idempotent(): void
    {
        $this->seed(ProductionUserSeeder::class);
        $this->seed(ProductionUserSeeder::class);

        $this->assertSame(3, User::query()->count());
    }
}
