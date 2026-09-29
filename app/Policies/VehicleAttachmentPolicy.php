<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\VehicleAttachment;

final class VehicleAttachmentPolicy
{
    public function view(User $user, VehicleAttachment $attachment): bool
    {
        return (new VehiclePolicy)->view($user, $attachment->vehicle);
    }
}
