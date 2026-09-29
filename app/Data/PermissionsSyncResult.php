<?php

declare(strict_types=1);

namespace App\Data;

final readonly class PermissionsSyncResult
{
    /**
     * @param  list<int>  $userIds
     */
    public function __construct(
        public int $synced,
        public array $userIds,
    ) {}
}
