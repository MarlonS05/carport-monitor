<?php

declare(strict_types=1);

namespace App\Livewire\Monitor;

use App\Models\ServiceItem;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class Dashboard extends Component
{
    /**
     * @return Collection<int, Vehicle>
     */
    #[Computed]
    public function vehicles(): Collection
    {
        /** @var User $user */
        $user = auth()->user();

        return Vehicle::query()
            ->visibleTo($user)
            ->withCount('serviceItems')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function vehicleCount(): int
    {
        return $this->vehicles->count();
    }

    #[Computed]
    public function totalLogCount(): int
    {
        /** @var User $user */
        $user = auth()->user();

        return ServiceItem::query()->visibleTo($user)->count();
    }

    #[Computed]
    public function lastSyncedAt(): CarbonInterface
    {
        /** @var User $user */
        $user = auth()->user();

        $timestamp = collect([
            Vehicle::query()->visibleTo($user)->max('updated_at'),
            ServiceItem::query()->visibleTo($user)->max('updated_at'),
        ])->filter()->max();

        return $timestamp !== null ? Carbon::parse($timestamp) : now();
    }

    public function render(): View
    {
        return view('livewire.monitor.dashboard')
            ->layout('layouts.monitor', [
                'activeNav' => 'dashboard',
                'connected' => true,
                'showHeader' => true,
            ]);
    }
}
