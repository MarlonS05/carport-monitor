# App Shell

> Global layout wrapping all Carport Monitor screens. See
> [README.md](README.md) for navigation flow.

---

## Structure

```
┌──────────────┬─────────────────────────────────────────────┐
│              │  Top header (connected only, not connect)   │
│   Sidebar    ├─────────────────────────────────────────────┤
│   240px      │                                             │
│              │           Main content (scrollable)         │
│              │                                             │
│              │                                             │
├──────────────┤                                             │
│ Session      │                                             │
│ status       │                                             │
└──────────────┴─────────────────────────────────────────────┘
```

- Root: `h-screen`, `bg-background`, `overflow-hidden`, `dark` class
- Horizontal split: sidebar + `flex-1 flex flex-col min-w-0` main column
- Main column: optional header + `flex-1 overflow-hidden` screen slot

---

## Sidebar

**Width:** `240px` fixed · **Background:** `sidebar` (`#161618`) · **Border:** right `border-border`

### Logo block (top)

- Padding: `px-6 py-7`, bottom border
- Row: `36×36` icon chip (`primary` at 15% opacity bg, `Car` icon @17px in `primary`)
- Two-line wordmark:
  - Line 1: **CARPORT** — Barlow Condensed, `text-lg`, extrabold, uppercase, wide tracking, `foreground`
  - Line 2: **MONITOR** — same style, `mutedForeground`

### Navigation

Two items, vertical stack with `space-y-1`, horizontal padding `px-3`, vertical `py-4`:

| ID | Label | Icon | Notes |
|----|-------|------|-------|
| `dashboard` | DASHBOARD | `LayoutDashboard` | |
| `connection` | CONNECTION | `Link2` | Trailing status dot (see below) |

**Item layout:** full width, `flex items-center gap-3`, `px-3 py-2.5`, `rounded-lg`, left-aligned.

**States:**

| State | Background | Text / icon |
|-------|------------|-------------|
| Active | `primary @ 10%` | `primary` |
| Inactive | transparent | `mutedForeground` |
| Inactive hover | `secondary` | `foreground` |

Label: DM Mono, `text-[10px]`, uppercase, wide tracking.

**Connection nav indicator:** `8×8` dot on the right — `success` when connected, `mutedForeground` when not.

### Footer (bottom)

- Padding: `px-6 py-5`, top border
- Row 1: `Wifi` (`success`) or `WifiOff` (`mutedForeground`) + **SESSION ACTIVE** / **NOT CONNECTED** (mono `9px`, `mutedForeground`)
- Row 2: **V1.4.2 · READ-ONLY** (mono `9px`, `#444442`)

---

## Top header

Shown when `connected === true` and current screen is **not** connect.

- Height: `56px` (`h-14`)
- Border bottom `border-border`
- Horizontal padding: `32px` (`px-8`)
- `flex items-center justify-end`

**Content:** Muted disclaimer — **READ-ONLY · NO EDITS AVAILABLE** (mono `9px`, ~50% opacity)

Connection status is shown in the sidebar footer and connection nav dot — not in the header.

---

## Connection lifecycle

| Event | UI effect |
|-------|-----------|
| Pair success | `connected = true`, navigate to dashboard, `activeNav = dashboard` |
| Disconnect (sidebar → Connection) | `connected = false`, navigate to connect, `activeNav = connection`, clear selected vehicle |
| Sidebar → Connection | Show connect screen, clear selected vehicle |

---

## Livewire / Blade notes

- Shell is a persistent layout component; screen content swaps via `@livewire` or nested routes.
- Sidebar `activeNav` can derive from route name (`monitor.dashboard`, `monitor.connect`, etc.).
- Header and sidebar stay mounted; only the main slot re-renders on navigation.
