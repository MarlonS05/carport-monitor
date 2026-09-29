<?php

declare(strict_types=1);

namespace App\Livewire\Monitor;

use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

final class VehicleDetail extends Component
{
    use AuthorizesRequests;

    public Vehicle $vehicle;

    public function mount(Vehicle $vehicle): void
    {
        $this->authorize('view', $vehicle);

        $this->vehicle = $vehicle
            ->loadCount('serviceItems')
            ->load(['attachments' => fn ($query) => $query->orderBy('created_at')]);
    }

    public function render(): View
    {
        return view('livewire.monitor.vehicle-detail')
            ->layout('layouts.monitor', [
                'activeNav' => 'dashboard',
                'connected' => true,
                'showHeader' => true,
            ]);
    }
}
