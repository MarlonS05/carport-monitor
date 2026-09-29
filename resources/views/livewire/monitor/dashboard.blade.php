@php
    $lastSyncRelative = $this->lastSyncedAt->diffForHumans(null, true, false, 2);
    $lastSyncClock = $this->lastSyncedAt->format('H:i');
@endphp

<div class="h-full overflow-y-auto p-8">
    <div class="space-y-8">
        <header>
            <p class="mb-2 font-mono text-[11px] tracking-widest text-primary uppercase">
                Dashboard
            </p>
            <h1 class="font-display text-5xl leading-none font-extrabold tracking-wide text-foreground uppercase">
                Carport
            </h1>
            <p class="font-display text-5xl leading-none font-extrabold tracking-wide text-muted-foreground uppercase">
                Monitor
            </p>
        </header>

        <section aria-label="Garage statistics">
            <div class="grid grid-cols-3 gap-3">
                <x-monitor.stat-card
                    label="Vehicles"
                    :value="$this->vehicleCount"
                    sublabel="In garage"
                />
                <x-monitor.stat-card
                    label="Total logs"
                    :value="$this->totalLogCount"
                    sublabel="All vehicles"
                />
                <x-monitor.stat-card
                    label="Last sync"
                    :value="strtoupper($lastSyncRelative)"
                    :sublabel="'Ago · '.$lastSyncClock.' local'"
                />
            </div>
        </section>

        <section aria-label="Vehicles">
            <x-monitor.section-label text="Vehicles" />

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                @foreach ($this->vehicles as $vehicle)
                    <x-monitor.vehicle-list-card
                        :vehicle="$vehicle"
                        :href="route('monitor.vehicles.show', $vehicle)"
                        wire:key="vehicle-{{ $vehicle->external_id }}"
                    />
                @endforeach
            </div>
        </section>

        <footer class="flex items-center gap-2 pt-2">
            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-success" aria-hidden="true"></span>
            <p class="font-mono text-[10px] tracking-widest text-muted-foreground uppercase">
                Read-only mirror · Changes made in mobile app
            </p>
        </footer>
    </div>
</div>
