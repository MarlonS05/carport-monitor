@props([
    'label',
    'value',
    'sublabel' => null,
])

<div {{ $attributes->class(['flex flex-col gap-2 rounded-xl border border-border bg-card p-4']) }}>
    <span class="font-mono text-xs tracking-widest text-muted-foreground uppercase">
        {{ $label }}
    </span>
    <span class="font-display text-3xl leading-none font-bold tracking-wide text-foreground">
        {{ $value }}
    </span>
    @if ($sublabel)
        <span class="font-mono text-[10px] text-muted-foreground uppercase">
            {{ $sublabel }}
        </span>
    @endif
</div>
