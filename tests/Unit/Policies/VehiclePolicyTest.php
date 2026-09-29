<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Mobile;
use App\Models\User;
use App\Models\Vehicle;
use App\Policies\VehiclePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class VehiclePolicyTest extends TestCase
{
    use RefreshDatabase;

    private VehiclePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new VehiclePolicy;
    }

    public function test_view_allows_unassigned_vehicle(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['mobile_id' => null]);

        $this->assertTrue($this->policy->view($user, $vehicle));
    }

    public function test_view_allows_linked_user(): void
    {
        $mobile = Mobile::factory()->create();
        $user = User::factory()->create();
        $mobile->users()->attach($user);

        $vehicle = Vehicle::factory()->create(['mobile_id' => $mobile->id]);

        $this->assertTrue($this->policy->view($user, $vehicle));
    }

    public function test_view_denies_unlinked_user(): void
    {
        $mobile = Mobile::factory()->create();
        $user = User::factory()->create();

        $vehicle = Vehicle::factory()->create(['mobile_id' => $mobile->id]);

        $this->assertFalse($this->policy->view($user, $vehicle));
    }
}
