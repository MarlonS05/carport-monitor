<?php

declare(strict_types=1);

namespace Tests\Feature\Monitor;

use App\Enums\ColorTheme;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ThemePickerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TestDatabaseSeeder::class);
        $this->loginAsSeededUser('alex@carport.local');
    }

    public function test_dashboard_renders_theme_picker(): void
    {
        $response = $this->get(route('monitor.dashboard'));

        $response->assertOk();
        $response->assertSee('Appearance');
        $response->assertSee('carport-monitor-theme', false);

        foreach (ColorTheme::cases() as $theme) {
            $response->assertSee('value="'.$theme->value.'"', false);
            $response->assertSee($theme->label(), false);
        }

        $response->assertSee('data-theme-picker', false);
    }

    public function test_guest_layout_includes_theme_init_script(): void
    {
        auth()->logout();

        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('carport-monitor-theme', false);
        $response->assertSee("document.documentElement.setAttribute('data-theme'", false);
        $response->assertDontSee('Appearance');
    }
}
