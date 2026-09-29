<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    /**
     * @var list<array{name: string, email: string}>
     */
    private const USERS = [
        ['name' => 'Alex Morgan', 'email' => 'alex@carport.local'],
        ['name' => 'Jordan Lee', 'email' => 'jordan@carport.local'],
        ['name' => 'Sam Rivera', 'email' => 'sam@carport.local'],
    ];

    public function run(): void
    {
        foreach (self::USERS as $user) {
            User::factory()->create($user);
        }
    }
}
