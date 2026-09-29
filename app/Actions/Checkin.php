<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CheckinResult;
use App\Models\ServiceItem;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleAttachment;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

final class Checkin
{
    public function handle(CarbonInterface $mobileUpdatedAt): CheckinResult
    {
        $portalLastUpdated = collect([
            Vehicle::query()->max('updated_at'),
            ServiceItem::query()->max('updated_at'),
            VehicleAttachment::query()->max('updated_at'),
        ])->filter()->max();

        $isDatabaseSync = $portalLastUpdated !== null
            && $mobileUpdatedAt->startOfSecond()->lte(Carbon::parse($portalLastUpdated)->startOfSecond());

        /** @var array<string, string> $users */
        $users = User::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->mapWithKeys(static fn (string $name, int|string $id): array => [(string) $id => $name])
            ->all();

        return new CheckinResult($users, $isDatabaseSync);
    }
}
