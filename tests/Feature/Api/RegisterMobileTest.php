<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Mobile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class RegisterMobileTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_mobile_and_returns_uuid(): void
    {
        $response = $this->getJson('/api/register');

        $response->assertOk();
        $response->assertJsonStructure(['id']);
        $response->assertJsonCount(1);

        $id = $response->json('id');

        $this->assertIsString($id);
        $this->assertTrue(Str::isUuid($id));

        $this->assertDatabaseHas('mobiles', ['id' => $id]);
        $this->assertDatabaseCount('mobiles', 1);
    }

    public function test_each_register_request_creates_a_distinct_mobile(): void
    {
        $first = $this->getJson('/api/register')->json('id');
        $second = $this->getJson('/api/register')->json('id');

        $this->assertNotSame($first, $second);
        $this->assertDatabaseCount('mobiles', 2);
    }

    public function test_register_persists_mobile_with_timestamps(): void
    {
        $this->getJson('/api/register');

        $mobile = Mobile::query()->first();

        $this->assertNotNull($mobile);
        $this->assertNotNull($mobile->created_at);
        $this->assertNotNull($mobile->updated_at);
    }

    public function test_register_grants_all_users_permission_to_view_mobile_data(): void
    {
        $alex = User::factory()->create();
        $jordan = User::factory()->create();
        $sam = User::factory()->create();

        $response = $this->getJson('/api/register');

        $response->assertOk();

        $mobileId = $response->json('id');

        foreach ([$alex, $jordan, $sam] as $user) {
            $this->assertDatabaseHas('mobile_user', [
                'mobile_id' => $mobileId,
                'user_id' => $user->id,
            ]);
        }
    }
}
