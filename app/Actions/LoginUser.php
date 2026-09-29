<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class LoginUser
{
    public function handle(User $user, bool $remember = false): void
    {
        Auth::login($user, $remember);
    }
}
