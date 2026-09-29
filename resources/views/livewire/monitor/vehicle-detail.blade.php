<div class="h-full overflow-y-auto p-8">
    <div class="space-y-6">
        <x-monitor.back-link
            :href="route('monitor.dashboard')"
            label="Back to dashboard"
        />

        <div class="flex items-center gap-3">
            <x-monitor.icon-chip variant="amber" icon="car" />
            <div>
                <p class="font-mono text-[10px] tracking-widest text-muted-foreground uppercase">
                    Vehicle
                </p>
                <p class="font-sans text-[15px] font-medium text-foreground">
                    {{ $vehicle->name }}
                </p>
            </div>
        </div>

        <div class="border-t border-border"></div>

        <section>
            <p class="mb-2 font-mono text-[10px] tracking-widest text-muted-foreground uppercase">
                Description
            </p>
            @if ($vehicle->description)
                <p class="font-sans text-sm leading-relaxed whitespace-pre-line text-foreground">
                    {{ $vehicle->description }}
                </p>
            @else
                <p class="font-sans text-sm text-foreground">—</p>
            @endif
        </section>

        <section>
            <p class="mb-2 font-mono text-[10px] tracking-widest text-muted-foreground uppercase">
                Mileage
            </p>
            <div class="flex items-center gap-3">
                <x-monitor.icon-chip icon="gauge" />
                <p class="font-display text-3xl font-bold tracking-wide text-foreground">
                    {{ number_format($vehicle->mileage) }}
                    <span class="text-xl text-muted-foreground uppercase">{{ $vehicle->mileage_unit->value }}</span>
                </p>
            </div>
        </section>

        <section>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="mb-2 font-mono text-[10px] tracking-widest text-muted-foreground uppercase">
                        Maintenance schedule
                    </p>
                    <div class="aspect-square overflow-hidden rounded-xl border border-border bg-secondary">
                        @if ($vehicle->maintenanceScheduleImageDataUri())
                            <img
                                src="{{ $vehicle->maintenanceScheduleImageDataUri() }}"
                                alt="Maintenance schedule for {{ $vehicle->name }}"
                                class="h-full w-full object-contain"
                            />
                        @else
                            <div class="flex h-full flex-col items-center justify-center gap-2 opacity-40">
                                <x-monitor.icon name="file-text" :size="28" />
                                <span class="font-mono text-[10px] tracking-widest uppercase">No image</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <p class="mb-2 font-mono text-[10px] tracking-widest text-muted-foreground uppercase">
                        Attachments
                    </p>
                    <div class="aspect-square overflow-y-auto rounded-xl border border-border bg-secondary p-3">
                        @if ($vehicle->attachments->isEmpty())
                            <div class="flex h-full flex-col items-center justify-center gap-2 opacity-40">
                                <x-monitor.icon name="file-text" :size="28" />
                                <span class="font-mono text-[10px] tracking-widest uppercase">No files</span>
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach ($vehicle->attachments as $attachment)
                                    <a
                                        href="{{ route('monitor.vehicles.attachments.download', [$vehicle, $attachment]) }}"
                                        class="flex items-center gap-3 rounded-xl border border-border bg-card p-3 transition-colors duration-150 hover:border-primary/20 hover:bg-secondary"
                                    >
                                        <x-monitor.icon-chip icon="file-text" />
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate font-sans text-sm text-foreground">
                                                {{ $attachment->original_filename }}
                                            </p>
                                            <p class="font-mono text-[10px] tracking-widest text-muted-foreground uppercase">
                                                {{ $attachment->formattedSize() }}
                                            </p>
                                        </div>
                                        <x-monitor.icon name="external-link" :size="14" class="shrink-0 text-muted-foreground" />
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section>
            <p class="mb-2 font-mono text-[10px] tracking-widest text-muted-foreground uppercase">
                Links
            </p>

            <div class="space-y-2">
                @if ($vehicle->userManualHref())
                    <a
                        href="{{ $vehicle->userManualHref() }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-3 rounded-xl border border-border bg-card p-4 transition-colors duration-150 hover:border-primary/20 hover:bg-secondary"
                    >
                        <x-monitor.icon-chip icon="external-link" />
                        <span class="flex-1 font-sans text-sm text-foreground">
                            User manual
                        </span>
                        <x-monitor.icon name="external-link" :size="14" class="text-muted-foreground" />
                    </a>
                @else
                    <x-monitor.card class="flex items-center gap-3 opacity-60">
                        <x-monitor.icon-chip icon="external-link" />
                        <span class="flex-1 font-sans text-sm text-foreground">
                            User manual
                        </span>
                        <x-monitor.icon name="external-link" :size="14" class="text-muted-foreground" />
                    </x-monitor.card>
                @endif

                @if ($vehicle->serviceManualHref())
                    <a
                        href="{{ $vehicle->serviceManualHref() }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-3 rounded-xl border border-border bg-card p-4 transition-colors duration-150 hover:border-primary/20 hover:bg-secondary"
                    >
                        <x-monitor.icon-chip icon="external-link" />
                        <span class="flex-1 font-sans text-sm text-foreground">
                            Service manual
                        </span>
                        <x-monitor.icon name="external-link" :size="14" class="text-muted-foreground" />
                    </a>
                @else
                    <x-monitor.card class="flex items-center gap-3 opacity-60">
                        <x-monitor.icon-chip icon="external-link" />
                        <span class="flex-1 font-sans text-sm text-foreground">
                            Service manual
                        </span>
                        <x-monitor.icon name="external-link" :size="14" class="text-muted-foreground" />
                    </x-monitor.card>
                @endif
            </div>
        </section>

        <div class="border-t border-border"></div>

        <a
            href="{{ route('monitor.vehicles.service-log', $vehicle) }}"
            class="group flex min-h-12 w-full items-center gap-3 rounded-xl border border-border bg-card p-4 transition-colors duration-150 hover:border-primary/20 hover:bg-secondary"
        >
            <x-monitor.icon-chip variant="amber" icon="wrench" />
            <div class="min-w-0 flex-1">
                <p class="font-mono text-[10px] tracking-widest text-muted-foreground uppercase">
                    Service log
                </p>
                <p class="font-sans text-sm text-foreground">
                    {{ $vehicle->entryCount() }} {{ Str::plural('entry', $vehicle->entryCount()) }} recorded
                </p>
            </div>
            <x-monitor.icon
                name="chevron-right"
                :size="16"
                class="shrink-0 text-muted-foreground group-hover:text-primary"
            />
        </a>
    </div>
</div>
