@props([
    'text',
])

<p {{ $attributes->class(['mb-4 font-mono text-[11px] tracking-widest text-muted-foreground uppercase']) }}>
    {{ $text }}
</p>
