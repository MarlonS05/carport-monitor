<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

final class VehiclePolicy
{
    public function view(User $user, Vehicle $vehicle): bool
    {
        return Vehicle::query()
            ->whereKey($vehicle->id)
            ->visibleTo($user)
            ->exists();
    }
}
