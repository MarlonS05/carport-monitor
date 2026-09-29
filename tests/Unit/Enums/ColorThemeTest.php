<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\ColorTheme;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class ColorThemeTest extends TestCase
{
    public function test_default_returns_legacy(): void
    {
        $this->assertSame(ColorTheme::Legacy, ColorTheme::default());
        $this->assertSame('legacy', ColorTheme::default()->value);
    }

    /**
     * @return array<string, array{ColorTheme, string, string}>
     */
    public static function themeProvider(): array
    {
        return [
            'legacy' => [ColorTheme::Legacy, 'legacy', 'Legacy'],
            'ocean depth' => [ColorTheme::OceanDepth, 'oceanDepth', 'Ocean Depth'],
            'swamp fog' => [ColorTheme::SwampFog, 'swampFog', 'Swamp Fog'],
            'sub zero' => [ColorTheme::SubZero, 'subZero', 'Sub Zero'],
            'mountain sunrise' => [ColorTheme::MountainSunrise, 'mountainSunrise', 'Mountain Sunrise'],
        ];
    }

    #[DataProvider('themeProvider')]
    public function test_theme_values_and_labels(ColorTheme $theme, string $value, string $label): void
    {
        $this->assertSame($value, $theme->value);
        $this->assertSame($label, $theme->label());
        $this->assertNotSame('', $theme->accentColor());
        $this->assertStringStartsWith('#', $theme->accentColor());
    }

    public function test_all_cases_are_covered(): void
    {
        $this->assertCount(5, ColorTheme::cases());
    }

    /**
     * @return array<string, array{string, ColorTheme}>
     */
    public static function storageMigrationProvider(): array
    {
        return [
            'dark colorful' => ['darkColorful', ColorTheme::Legacy],
            'dark light blue' => ['darkLightBlue', ColorTheme::OceanDepth],
            'dark plain' => ['darkPlain', ColorTheme::MountainSunrise],
            'light plain' => ['lightPlain', ColorTheme::SubZero],
            'light colorful' => ['lightColorful', ColorTheme::MountainSunrise],
            'unknown' => ['unknown', ColorTheme::Legacy],
            'null' => [null, ColorTheme::Legacy],
        ];
    }

    #[DataProvider('storageMigrationProvider')]
    public function test_from_storage_migrates_legacy_keys(?string $stored, ColorTheme $expected): void
    {
        $this->assertSame($expected, ColorTheme::fromStorage($stored));
    }
}
