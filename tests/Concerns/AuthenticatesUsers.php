<?php

declare(strict_types=1);

namespace Tests\Concerns;

use App\Models\User;

trait AuthenticatesUsers
{
    protected function loginAsUser(?User $user = null): User
    {
        $user ??= User::factory()->create();

        $this->actingAs($user);

        return $user;
    }

    protected function loginAsSeededUser(string $email = 'alex@carport.local'): User
    {
        $user = User::query()->where('email', $email)->firstOrFail();

        $this->actingAs($user);

        return $user;
    }
}
