@props([
    'variant' => 'default',
    'icon' => 'car',
    'iconSize' => 18,
])

@php
    $variantClasses = match ($variant) {
        'amber' => 'bg-primary/15 text-primary',
        default => 'bg-secondary text-muted-foreground',
    };
@endphp

<div
    {{ $attributes->class([
        'flex h-11 w-11 shrink-0 items-center justify-center rounded-lg',
        $variantClasses,
    ]) }}
>
    <x-monitor.icon :name="$icon" :size="$iconSize" />
</div>
