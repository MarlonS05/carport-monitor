<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class ProductionUserSeeder extends Seeder
{
    /**
     * @var list<array{name: string, email: string}>
     */
    private const USERS = [
        ['name' => 'Pete', 'email' => 'pete@carport.local'],
        ['name' => 'Garfield', 'email' => 'garfield@carport.local'],
        ['name' => 'Amsterdam', 'email' => 'amsterdam@carport.local'],
    ];

    public function run(): void
    {
        foreach (self::USERS as $user) {
            User::query()->firstOrCreate(
                ['email' => $user['email']],
                User::factory()->make($user)->getAttributes(),
            );
        }
    }
}
