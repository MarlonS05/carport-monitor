<div class="h-full overflow-y-auto p-8">
    <div class="space-y-6">
        <x-monitor.back-link
            :href="route('monitor.vehicles.show', $vehicle)"
            label="Back to vehicle"
        />

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="font-display text-3xl font-bold tracking-wide text-foreground uppercase">
                    Service log
                </h1>
                <span class="rounded-lg border border-border bg-secondary px-2.5 py-1 font-mono text-[10px] tracking-widest text-primary uppercase">
                    {{ $this->entries->count() }} {{ Str::plural('entry', $this->entries->count()) }}
                </span>
            </div>

            <div class="flex items-center gap-2 rounded-lg border border-border bg-secondary px-3 py-2">
                <x-monitor.icon name="car" :size="12" class="text-muted-foreground" />
                <span class="font-sans text-[13px] text-muted-foreground">
                    {{ $vehicle->name }}
                </span>
            </div>
        </div>

        <label class="flex items-center gap-2 rounded-lg border border-border bg-secondary px-3 py-2.5">
            <x-monitor.icon name="search" :size="14" class="text-muted-foreground" />
            <input
                type="search"
                placeholder="Search entries…"
                class="w-full bg-transparent font-sans text-[13px] text-foreground outline-none placeholder:text-muted-foreground"
            />
        </label>

        @if ($this->entries->isEmpty())
            <div class="flex flex-col items-center gap-3 py-16 opacity-40">
                <x-monitor.icon name="file-text" :size="32" class="text-muted-foreground" />
                <p class="font-mono text-[11px] tracking-widest text-muted-foreground uppercase">
                    No entries yet
                </p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($this->entries as $entry)
                    <x-monitor.card wire:key="entry-{{ $entry->external_id }}">
                        <div class="flex items-start gap-3">
                            <x-monitor.icon-chip variant="amber" icon="wrench" />

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-4">
                                    <h2 class="font-display text-xl font-bold tracking-wide text-foreground uppercase">
                                        {{ $entry->title }}
                                    </h2>
                                    <span class="shrink-0 font-mono text-[10px] text-muted-foreground uppercase">
                                        {{ $entry->formattedDate() }}
                                    </span>
                                </div>

                                <p class="mt-1 flex items-center gap-1.5 font-mono text-[10px] text-muted-foreground uppercase">
                                    <x-monitor.icon name="gauge" :size="11" />
                                    {{ $entry->formattedMileage() }}
                                </p>

                                @if ($entry->description)
                                    <p class="mt-2 ml-14 font-sans text-[13px] leading-relaxed text-muted-foreground">
                                        {{ $entry->description }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </x-monitor.card>
                @endforeach
            </div>
        @endif
    </div>
</div>
