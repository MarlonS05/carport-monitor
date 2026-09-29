<?php

declare(strict_types=1);

namespace Tests\Feature\Monitor;

use App\Models\Mobile;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class VehicleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TestDatabaseSeeder::class);
    }

    public function test_unauthorized_user_cannot_view_mobile_owned_vehicle_detail(): void
    {
        $this->loginAsSeededUser('sam@carport.local');

        $response = $this->get(route('monitor.vehicles.show', 'mazda-cx5-2019'));

        $response->assertNotFound();
    }

    public function test_unauthorized_user_cannot_view_mobile_owned_service_log(): void
    {
        $this->loginAsSeededUser('sam@carport.local');

        $response = $this->get(route('monitor.vehicles.service-log', 'mazda-cx5-2019'));

        $response->assertNotFound();
    }

    public function test_unauthorized_user_does_not_see_mobile_owned_vehicles_on_dashboard(): void
    {
        $this->loginAsSeededUser('sam@carport.local');

        $response = $this->get(route('monitor.dashboard'));

        $response->assertOk();
        $response->assertDontSee('2019 Mazda CX-5');
        $response->assertDontSee('2016 Honda CB500F');
        $response->assertSee('Unassigned Demo Vehicle');
    }

    public function test_authorized_user_sees_mobile_owned_and_unassigned_vehicles(): void
    {
        $this->loginAsSeededUser('alex@carport.local');

        $response = $this->get(route('monitor.dashboard'));

        $response->assertOk();
        $response->assertSee('2019 Mazda CX-5');
        $response->assertSee('2016 Honda CB500F');
        $response->assertSee('Unassigned Demo Vehicle');
    }

    public function test_unassigned_vehicle_is_visible_to_any_authenticated_user(): void
    {
        $mobile = Mobile::factory()->create();
        $user = User::factory()->create();

        Vehicle::factory()->create([
            'external_id' => 'shared-unassigned-vehicle',
            'mobile_id' => null,
            'name' => 'Shared Unassigned Vehicle',
        ]);

        Vehicle::factory()->create([
            'external_id' => 'private-mobile-vehicle',
            'mobile_id' => $mobile->id,
            'name' => 'Private Mobile Vehicle',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('monitor.vehicles.show', 'shared-unassigned-vehicle'));

        $response->assertOk();
        $response->assertSee('Shared Unassigned Vehicle');
    }
}
