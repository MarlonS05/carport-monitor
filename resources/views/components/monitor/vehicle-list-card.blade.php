@props([
    'vehicle',
    'href' => '#',
])

<a
    href="{{ $href }}"
    {{ $attributes->class([
        'group flex min-h-12 w-full items-center gap-3 rounded-xl border border-border bg-card p-4',
        'transition-colors duration-150 hover:border-primary/20 hover:bg-secondary',
    ]) }}
>
    <x-monitor.icon-chip variant="amber" icon="car" />

    <div class="min-w-0 flex-1">
        <p class="truncate font-sans text-sm font-medium text-foreground">
            {{ $vehicle->name }}
        </p>
        <p class="mt-0.5 font-mono text-[10px] text-muted-foreground uppercase">
            {{ $vehicle->formattedMileage() }}
            <span aria-hidden="true"> · </span>
            {{ $vehicle->entryCount() }} {{ Str::upper(Str::plural('entry', $vehicle->entryCount())) }}
        </p>
    </div>

    <x-monitor.icon
        name="chevron-right"
        :size="16"
        class="shrink-0 text-muted-foreground group-hover:text-primary"
    />
</a>
