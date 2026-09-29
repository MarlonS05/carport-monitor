# Shared Components

> Reusable UI building blocks used across Carport Monitor screens. Token values
> reference [design-language.md](../design-language.md).

---

## Icon chip

Square leading icon container used in lists and headers.

| Property | Value |
|----------|--------|
| Size | `44×44` (`w-11 h-11`) |
| Radius | `rounded-lg` (8px) |
| Layout | Centered icon |

**Variants:**

| Variant | Background | Icon color | Icon size |
|---------|------------|------------|-----------|
| Default | `secondary` | `mutedForeground` | 18px |
| Amber | `primary @ 15%` | `primary` | 18px |

---

## Stat card

Dashboard metric cell.

| Property | Value |
|----------|--------|
| Container | Standard card — `bg-card`, `border-border`, `rounded-xl`, `p-4` |
| Layout | Column, `gap-2` |
| Label | Mono `12px`, uppercase, wide tracking, `mutedForeground` |
| Value | Barlow Condensed `text-3xl`, bold, `foreground`, tight leading, wide tracking |
| Sub-label | Optional — mono `10px`, `#888884` |

---

## Standard card

Base surface for rows and content blocks.

```
bg-card · border border-border · rounded-xl · p-4
transition-colors duration-150
```

**Interactive hover** (list rows):

- Background → `secondary`
- Border → `primary @ 20%` (`rgba(232,124,42,0.2)`)

---

## Vehicle list card

Tappable row on dashboard. Composes: [standard card](#standard-card) + [icon chip](#icon-chip) + chevron.

Min tap height: follow `minTap` (48px) from design language.

---

## Typography shortcuts (prototype)

Repeated class patterns in the Make source:

| Alias | Classes |
|-------|---------|
| `mono` | `font-mono text-xs tracking-widest uppercase` |
| Hero condensed | `font-['Barlow_Condensed']` + size/weight per screen |
| Body | `font-['DM_Sans']` |

**Fonts** (Google Fonts): Barlow Condensed 600/700/800, DM Mono 400/500, DM Sans 300/400/500.

---

## Color usage beyond design language

| Hex | Role in Monitor UI |
|-----|-------------------|
| `#161618` | Sidebar background (`--sidebar`) |
| `#444442` | Sidebar version footnote |
| `#34D399` | Live / connected indicators (`success`) |
| `#d06f22` | Primary button hover (prototype) |

---

## Icons by screen

| Screen | Icons |
|--------|-------|
| App shell | `Car`, `LayoutDashboard`, `Link2`, `Wifi`, `WifiOff` |
| Connect | `Wifi` |
| Dashboard | `Car`, `ChevronRight` |
| Vehicle detail | `ArrowLeft`, `Car`, `Gauge`, `FileText`, `ExternalLink`, `Wrench`, `ChevronRight` |
| Service log | `ArrowLeft`, `Car`, `Search`, `Wrench`, `Gauge`, `FileText` |

---

## Motion

- Card / nav hover: `transition-colors duration-150` (aligns with `standard` 200ms in design language)
- Pairing dot: CSS `animate-pulse` on `primary` dot
- No page transitions specified in prototype

---

## Blade component mapping (suggested)

| Make concept | Blade component |
|--------------|-----------------|
| Icon chip | `<x-monitor.icon-chip>` |
| Stat card | `<x-monitor.stat-card>` |
| Standard card | `<x-monitor.card>` |
| Section label | `<x-monitor.section-label>` |
| Back link | `<x-monitor.back-link>` |

Implement with Tailwind utilities backed by CSS variables from Industrial Dark tokens.
