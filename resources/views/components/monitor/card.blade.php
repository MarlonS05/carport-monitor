@props([
    'interactive' => false,
])

<div
    {{ $attributes->class([
        'rounded-xl border border-border bg-card p-4 transition-colors duration-150',
        'hover:border-primary/20 hover:bg-secondary' => $interactive,
    ]) }}
>
    {{ $slot }}
</div>
