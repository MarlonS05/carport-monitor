# Dashboard

> Garage overview after a successful mobile pairing. Read-only summary of
> vehicles and sync status.

---

## Purpose

- Welcome / brand hero
- At-a-glance garage stats
- Vehicle list — entry point to [Vehicle Detail](vehicle-detail.md)
- Reinforce read-only mirror behavior

---

## Layout

- Scrollable main area: `flex-1 overflow-y-auto p-8 space-y-8`
- No back button (root screen when connected)

---

## Hero block

| Element | Content / style |
|---------|-----------------|
| Eyebrow | **DASHBOARD** — mono `11px`, `primary`, `mb-2` |
| Title line 1 | **CARPORT** — Barlow Condensed `text-5xl`, extrabold, uppercase, `foreground` |
| Title line 2 | **MONITOR** — same, `mutedForeground` |

---

## Stats row

Three-column grid (`grid-cols-3`, `gap-3`). Each cell is a [Stat card](shared-components.md#stat-card).

| Label | Example value | Sub-label |
|-------|---------------|-----------|
| VEHICLES | `2` | IN GARAGE |
| TOTAL LOGS | `19` | ALL VEHICLES |
| LAST SYNC | `2 MIN` | AGO · 14:38 LOCAL |

**Production:** bind to live counts and last sync timestamp from session API.

---

## Vehicles section

### Section label

**VEHICLES** — mono `11px`, `mutedForeground`, `mb-4`

### Vehicle grid

- `grid-cols-1` → `md:grid-cols-2`, `gap-3`
- Each item: tappable [list card](shared-components.md#vehicle-list-card) (full width button)

### Vehicle list card content

| Zone | Content |
|------|---------|
| Leading | [Icon chip](shared-components.md#icon-chip) — `Car` icon, amber variant |
| Body | Vehicle name (DM Sans `14px`, medium, `foreground`, truncate) |
| Meta row | Mileage formatted + unit · dot separator · `{n} ENTRIES` (mono `10px`, `mutedForeground`) |
| Trailing | `ChevronRight` @16px, `mutedForeground` |

**Interaction:** click → navigate to vehicle detail with selected vehicle ID.

**Hover:** card bg → `secondary`, border → `primary @ 20%`

---

## Footer note

Row with `6×6` `success` dot + mono `10px` text:

**READ-ONLY MIRROR · CHANGES MADE IN MOBILE APP**

`mutedForeground`, `pt-2` spacing above.

---

## Sample data (prototype)

| Vehicle | Mileage | Entries |
|---------|---------|---------|
| 2019 Mazda CX-5 | 42,350 km | 12 |
| 2016 Honda CB500F | 18,920 km | 7 |

---

## Livewire / Blade notes

- Component: `Dashboard` Livewire full-page or route view inside app shell
- Eager-load vehicle count + aggregate log count server-side
- Vehicle rows: `wire:click` or link to `monitor.vehicles.show`
- Last sync: format relative time + local clock from session metadata
