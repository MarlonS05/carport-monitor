<?php

declare(strict_types=1);

namespace App\Data;

final readonly class VehicleAttachmentsSyncResult
{
    /**
     * @param  list<string>  $attachmentIds
     */
    public function __construct(
        public int $deleted,
        public array $attachmentIds,
    ) {}
}
