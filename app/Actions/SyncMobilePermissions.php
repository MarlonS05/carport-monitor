<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\PermissionsSyncResult;
use App\Models\Mobile;

final class SyncMobilePermissions
{
    /**
     * @param  list<int>  $userIds
     */
    public function handle(Mobile $mobile, array $userIds): PermissionsSyncResult
    {
        $mobile->users()->sync($userIds);

        return new PermissionsSyncResult(
            synced: count($userIds),
            userIds: $userIds,
        );
    }
}
