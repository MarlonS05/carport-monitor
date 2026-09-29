<?php

declare(strict_types=1);

namespace App\Livewire\Monitor;

use App\Models\ServiceItem;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class ServiceLog extends Component
{
    use AuthorizesRequests;

    public Vehicle $vehicle;

    public function mount(Vehicle $vehicle): void
    {
        $this->authorize('view', $vehicle);

        $this->vehicle = $vehicle;
    }

    /**
     * @return Collection<int, ServiceItem>
     */
    #[Computed]
    public function entries(): Collection
    {
        return $this->vehicle
            ->serviceItems()
            ->newestFirst()
            ->get();
    }

    public function render(): View
    {
        return view('livewire.monitor.service-log')
            ->layout('layouts.monitor', [
                'activeNav' => 'dashboard',
                'connected' => true,
                'showHeader' => true,
            ]);
    }
}
