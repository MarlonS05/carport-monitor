<?php

declare(strict_types=1);

namespace Tests\Feature\Monitor;

use App\Models\ServiceItem;
use App\Models\Vehicle;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MonitorSampleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_sample_seeder_creates_garage_with_service_items(): void
    {
        $this->seed(TestDatabaseSeeder::class);

        $this->assertSame(3, Vehicle::query()->count());
        $this->assertSame(6, ServiceItem::query()->count());

        $mazda = Vehicle::query()->where('external_id', 'mazda-cx5-2019')->first();

        $this->assertNotNull($mazda);
        $this->assertSame('2019 Mazda CX-5', $mazda->name);
        $this->assertNotNull($mazda->mobile_id);
        $this->assertNull($mazda->user_manual_url);
        $this->assertNull($mazda->service_manual_url);
        $this->assertSame(4, $mazda->serviceItems()->count());

        $unassigned = Vehicle::query()->where('external_id', 'unassigned-demo-vehicle')->first();
        $this->assertNotNull($unassigned);
        $this->assertNull($unassigned->mobile_id);
    }
}
