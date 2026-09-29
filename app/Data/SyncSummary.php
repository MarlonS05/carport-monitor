<?php

declare(strict_types=1);

namespace App\Data;

final readonly class SyncSummary
{
    /**
     * @param  list<SyncItemResult>  $items
     */
    public function __construct(
        public int $created,
        public int $updated,
        public int $ignored,
        public array $items,
    ) {}
}
