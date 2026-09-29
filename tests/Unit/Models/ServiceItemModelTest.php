<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\ServiceItem;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ServiceItemModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_item_belongs_to_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create();
        $item = ServiceItem::factory()->for($vehicle)->create();

        $this->assertTrue($item->vehicle->is($vehicle));
    }

    public function test_newest_first_scope_orders_by_occurred_at_descending(): void
    {
        $vehicle = Vehicle::factory()->create();

        $older = ServiceItem::factory()->for($vehicle)->create([
            'external_id' => 'older',
            'occurred_at' => '2025-01-01',
        ]);

        $newer = ServiceItem::factory()->for($vehicle)->create([
            'external_id' => 'newer',
            'occurred_at' => '2026-03-04',
        ]);

        $results = ServiceItem::query()->newestFirst()->get();

        $this->assertTrue($results->first()->is($newer));
        $this->assertTrue($results->last()->is($older));
    }

    public function test_search_scope_matches_title_or_description(): void
    {
        $vehicle = Vehicle::factory()->create();

        ServiceItem::factory()->for($vehicle)->create([
            'external_id' => 'oil',
            'title' => 'OIL CHANGE',
            'description' => 'Synthetic oil',
        ]);

        ServiceItem::factory()->for($vehicle)->create([
            'external_id' => 'tires',
            'title' => 'TIRE ROTATION',
            'description' => 'All four tires',
        ]);

        $this->assertCount(1, ServiceItem::query()->search('oil')->get());
        $this->assertCount(1, ServiceItem::query()->search('rotation')->get());
        $this->assertCount(1, ServiceItem::query()->search('tire')->get());
    }
}
