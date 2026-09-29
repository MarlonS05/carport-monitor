<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Mobile;
use App\Models\User;

final class RegisterMobile
{
    public function handle(): Mobile
    {
        $mobile = Mobile::query()->create();

        $mobile->users()->sync(User::query()->pluck('id'));

        return $mobile;
    }
}
