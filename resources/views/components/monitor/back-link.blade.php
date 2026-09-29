@props([
    'href' => '#',
    'label',
])

<a
    href="{{ $href }}"
    {{ $attributes->class([
        'inline-flex items-center gap-2 font-mono text-[11px] tracking-widest text-muted-foreground uppercase',
        'transition-colors duration-150 hover:text-foreground',
    ]) }}
>
    <x-monitor.icon name="arrow-left" :size="16" />
    {{ $label }}
</a>
