<?php

declare(strict_types=1);

namespace App\Enums;

enum MileageUnit: string
{
    case Kilometers = 'km';
    case Miles = 'mi';

    public function label(): string
    {
        return match ($this) {
            self::Kilometers => 'KM',
            self::Miles => 'MI',
        };
    }
}
