# Service Log

> Searchable, read-only list of maintenance entries for one vehicle.

---

## Purpose

- Browse chronological service / maintenance records synced from mobile
- Filter entries client-side by title or description
- Show per-entry mileage, date, and notes

---

## Layout

- Scrollable: `flex-1 overflow-y-auto p-8 space-y-6`

---

## Top bar

- `ArrowLeft` @16px + **BACK TO VEHICLE** (mono `11px`)
- Returns to [Vehicle Detail](vehicle-detail.md) for the same vehicle

---

## Header row

`flex items-center justify-between flex-wrap gap-4`

### Left — title + count badge

| Element | Style |
|---------|--------|
| Title | **SERVICE LOG** — Barlow Condensed `text-3xl`, bold, uppercase, `foreground` |
| Badge | `secondary` bg, `border-border`, `rounded-lg`, `px-2.5 py-1` |
| Badge text | `{n} ENTRIES` — mono `10px`, `primary` |

### Right — vehicle context pill

- `secondary` bg, bordered, `rounded-lg`, `px-3 py-2`
- `Car` @12px + vehicle name (DM Sans `13px`, `mutedForeground`)
- Example: **2019 Mazda CX-5**

---

## Search

Full-width filter bar:

- Container: `secondary` bg, `border-border`, `rounded-lg`, `px-3 py-2.5`, `flex gap-2`
- Leading: `Search` @14px, `mutedForeground`
- Input: transparent bg, DM Sans `13px`, `foreground`, placeholder `mutedForeground`
- Placeholder: “Search entries…”
- No border on input; `outline-none`

**Filter logic:** case-insensitive match on `title` OR `description`. Empty query shows all.

---

## Entry list

Vertical stack `space-y-3`. Each entry is a standard card (`rounded-xl`, `p-4`, `border-border`).

### Entry card anatomy

```
┌────────────────────────────────────────────────────────────┐
│ [Wrench chip]  TITLE (condensed)              DATE         │
│                mileage row                                 │
│                description (indented)                      │
└────────────────────────────────────────────────────────────┘
```

| Zone | Detail |
|------|--------|
| Leading | Icon chip `Wrench`, amber |
| Title | Barlow Condensed `text-xl`, bold, uppercase, `foreground` |
| Mileage row | `Gauge` @11px + `{mileage} km` (mono `10px`, `mutedForeground`), `mt-1` |
| Date | Top-right, mono `10px`, `mutedForeground`, no wrap |
| Description | DM Sans `13px`, `mutedForeground`, relaxed; left margin `56px` (`ml-14`); omit if empty |

---

## Empty states

### No entries at all

Centered column, `py-16`:

- `FileText` @32px, `mutedForeground` @40% opacity
- **NO ENTRIES YET** (mono `11px`)

### No search results

Same empty treatment when filter returns zero rows (use distinct copy in production: **NO MATCHES**).

---

## Sample entries (Mazda CX-5)

| Title | Date | Mileage | Has description |
|-------|------|---------|-----------------|
| OIL CHANGE | Mar 4, 2026 | 42,350 km | Yes |
| TIRE ROTATION | Mar 4, 2026 | 42,350 km | Yes |
| BRAKE INSPECTION | Jan 12, 2026 | 40,100 km | Yes |
| … | … | … | … |

Titles are stored/displayed **uppercase** in the prototype.

---

## Data model

```text
LogEntry {
  id, title, date, description, mileage
}
```

Date format in prototype: `Mar 4, 2026` — align with mobile app locale rules.

---

## Livewire / Blade notes

- Scoped to `vehicle_id` from route; reject cross-vehicle access
- Search: `wire:model.live.debounce` on query string, filter collection in component
- Long lists: consider pagination or virtual scroll if sync delivers hundreds of rows
- Sort: prototype order is implicit (likely newest first from mock array)
