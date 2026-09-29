<?php

declare(strict_types=1);

namespace App\Enums;

enum SyncItemStatus: string
{
    case Created = 'created';
    case Updated = 'updated';
    case Ignored = 'ignored';
}
