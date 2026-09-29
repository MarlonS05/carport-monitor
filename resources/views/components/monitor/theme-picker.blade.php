@php
    use App\Enums\ColorTheme;
@endphp

<div class="mb-4">
    <label for="theme-picker" class="mb-2 block font-mono text-[9px] tracking-widest text-muted-foreground uppercase">
        Appearance
    </label>
    <select
        id="theme-picker"
        data-theme-picker
        class="w-full rounded-lg border border-border bg-input px-3 py-2 font-sans text-[12px] text-foreground transition-colors duration-150 focus:border-primary/40 focus:outline-none focus:ring-2 focus:ring-ring"
    >
        @foreach (ColorTheme::cases() as $theme)
            <option value="{{ $theme->value }}">{{ $theme->label() }}</option>
        @endforeach
    </select>
</div>
