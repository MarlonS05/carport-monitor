<?php

declare(strict_types=1);

namespace App\Enums;

enum ColorTheme: string
{
    case Legacy = 'legacy';
    case OceanDepth = 'oceanDepth';
    case SwampFog = 'swampFog';
    case SubZero = 'subZero';
    case MountainSunrise = 'mountainSunrise';

    public static function default(): self
    {
        return self::Legacy;
    }

    public static function fromStorage(?string $value): self
    {
        return match ($value) {
            'darkColorful' => self::Legacy,
            'darkLightBlue' => self::OceanDepth,
            'darkPlain' => self::MountainSunrise,
            'lightPlain' => self::SubZero,
            'lightColorful' => self::MountainSunrise,
            'legacy' => self::Legacy,
            'oceanDepth' => self::OceanDepth,
            'swampFog' => self::SwampFog,
            'subZero' => self::SubZero,
            'mountainSunrise' => self::MountainSunrise,
            default => self::default(),
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Legacy => 'Legacy',
            self::OceanDepth => 'Ocean Depth',
            self::SwampFog => 'Swamp Fog',
            self::SubZero => 'Sub Zero',
            self::MountainSunrise => 'Mountain Sunrise',
        };
    }

    public function accentColor(): string
    {
        return match ($this) {
            self::Legacy => '#e87c2a',
            self::OceanDepth => '#4a9fd4',
            self::SwampFog => '#6a9f7e',
            self::SubZero => '#4a9ec8',
            self::MountainSunrise => '#f0c078',
        };
    }
}
