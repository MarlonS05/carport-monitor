<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Actions\LoginUser;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class SelectUser extends Component
{
    /**
     * @return Collection<int, User>
     */
    #[Computed]
    public function users(): Collection
    {
        return User::query()
            ->orderBy('name')
            ->get();
    }

    public function login(int $userId, LoginUser $action): void
    {
        $user = User::query()->findOrFail($userId);

        $action->handle($user);

        $this->redirect(route('monitor.dashboard'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.auth.select-user')
            ->layout('layouts.guest');
    }
}
