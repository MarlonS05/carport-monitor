<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Mobile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SyncPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_creates_mobile_user_pivot_rows(): void
    {
        $mobile = Mobile::factory()->create();
        $alex = User::factory()->create();
        $jordan = User::factory()->create();

        $response = $this->postJson('/api/permissions/sync', [
            'user_ids' => [$alex->id, $jordan->id],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'synced' => 2,
            'user_ids' => [$alex->id, $jordan->id],
        ]);

        $this->assertDatabaseHas('mobile_user', [
            'mobile_id' => $mobile->id,
            'user_id' => $alex->id,
        ]);
        $this->assertDatabaseHas('mobile_user', [
            'mobile_id' => $mobile->id,
            'user_id' => $jordan->id,
        ]);
    }

    public function test_sync_replaces_existing_permissions(): void
    {
        $mobile = Mobile::factory()->create();
        $alex = User::factory()->create();
        $jordan = User::factory()->create();
        $sam = User::factory()->create();

        $mobile->users()->sync([$alex->id, $jordan->id]);

        $response = $this->postJson('/api/permissions/sync', [
            'user_ids' => [$sam->id],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'synced' => 1,
            'user_ids' => [$sam->id],
        ]);

        $this->assertDatabaseMissing('mobile_user', [
            'mobile_id' => $mobile->id,
            'user_id' => $alex->id,
        ]);
        $this->assertDatabaseHas('mobile_user', [
            'mobile_id' => $mobile->id,
            'user_id' => $sam->id,
        ]);
    }

    public function test_sync_with_empty_array_revokes_all_permissions(): void
    {
        $mobile = Mobile::factory()->create();
        $alex = User::factory()->create();

        $mobile->users()->sync([$alex->id]);

        $response = $this->postJson('/api/permissions/sync', [
            'user_ids' => [],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'synced' => 0,
            'user_ids' => [],
        ]);

        $this->assertDatabaseMissing('mobile_user', [
            'mobile_id' => $mobile->id,
            'user_id' => $alex->id,
        ]);
    }

    public function test_sync_requires_valid_mobile_id_header(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/permissions/sync', [
            'user_ids' => [$user->id],
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['mobile_id']);
    }

    public function test_sync_rejects_unknown_user_ids(): void
    {
        $mobile = Mobile::factory()->create();

        $response = $this->postJson('/api/permissions/sync', [
            'user_ids' => [999_999],
        ], [
            'X-Mobile-Id' => $mobile->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['user_ids.0']);
    }
}
