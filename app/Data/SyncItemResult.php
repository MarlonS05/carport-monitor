<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\SyncItemStatus;

final readonly class SyncItemResult
{
    public function __construct(
        public string $id,
        public SyncItemStatus $status,
    ) {}
}
