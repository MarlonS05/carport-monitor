<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Livewire\Auth\SelectUser;
use App\Models\User;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

final class SelectUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_lists_users(): void
    {
        $this->seed(TestDatabaseSeeder::class);

        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Select user');
        $response->assertSee('Alex Morgan');
        $response->assertSee('Jordan Lee');
        $response->assertSee('Sam Rivera');
    }

    public function test_guest_is_redirected_from_dashboard_to_login(): void
    {
        $response = $this->get(route('monitor.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_is_redirected_from_login_to_dashboard(): void
    {
        $user = $this->loginAsUser();

        $response = $this->get(route('login'));

        $response->assertRedirect(route('monitor.dashboard'));
    }

    public function test_user_can_sign_in_by_selecting_their_name(): void
    {
        $user = User::factory()->create([
            'name' => 'Casey Brooks',
            'email' => 'casey@carport.local',
        ]);

        Livewire::test(SelectUser::class)
            ->call('login', $user->id)
            ->assertRedirect(route('monitor.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_sign_out(): void
    {
        $user = $this->loginAsUser();

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
