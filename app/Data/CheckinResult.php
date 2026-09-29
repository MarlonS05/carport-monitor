<?php

declare(strict_types=1);

namespace App\Data;

final readonly class CheckinResult
{
    /**
     * @param  array<string, string>  $users
     */
    public function __construct(
        public array $users,
        public bool $isDatabaseSync,
    ) {}
}
