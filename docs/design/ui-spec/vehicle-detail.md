# Vehicle Detail

> Read-only profile for a single garage vehicle. Gateway to the
> [Service Log](service-log.md).

---

## Purpose

- Show vehicle identity and metadata from mobile sync
- Display mileage, description, maintenance schedule image, external links
- Navigate to full service log

---

## Layout

- Scrollable: `flex-1 overflow-y-auto p-8 space-y-6`

---

## Top bar

Text back control (not circular mobile back button):

- `ArrowLeft` @16px + **BACK TO DASHBOARD** (mono `11px`)
- `mutedForeground` → `foreground` on hover
- Action: return to [Dashboard](dashboard.md), clear drill-down state

---

## Vehicle header chip

Horizontal row (`gap-3`):

| Part | Content |
|------|---------|
| Icon chip | `Car`, amber variant |
| Label | **VEHICLE** — mono `10px`, `mutedForeground` |
| Name | Vehicle display name — DM Sans `15px`, medium, `foreground` |

Example: **2019 Mazda CX-5**

---

## Divider

Full-width `border-t border-border`

---

## Field sections

Vertical stack `space-y-6`.

### Description

| Label | **DESCRIPTION** — mono `10px`, `mutedForeground`, `mb-2` |
| Value | DM Sans `14px`, `foreground`, relaxed leading |
| Empty | Em dash `—` |

Example text: multi-line trim/color/purchase notes from mobile.

### Mileage

| Label | **MILEAGE** — mono `10px`, `mutedForeground`, `mb-2` |
| Value row | [Icon chip](shared-components.md#icon-chip) (`Gauge`, default) + large number |

Number: Barlow Condensed `text-3xl`, bold, `foreground`, wide tracking.  
Unit: `text-xl`, `mutedForeground`, uppercase (e.g. **KM**).

### Maintenance schedule

| Label | **MAINTENANCE SCHEDULE** — mono `10px`, `mutedForeground`, `mb-2` |
| Media | `aspect-square` container, `rounded-xl`, `secondary` bg, `border-border` |

Layout: 50/50 horizontal split with **Attachments** column on the right (same row, `grid-cols-2 gap-4`). Left column shows the maintenance schedule image; right column lists synced file attachments in a matching square scrollable frame.

**Empty state (no image):**

- Centered column, 40% opacity
- `FileText` @28px + **NO IMAGE** (mono `10px`)

**With image:** image fits inside the square frame with `object-contain` (no cropping).

### Attachments

| Label | **ATTACHMENTS** — mono `10px`, `mutedForeground`, `mb-2` |
| List | Scrollable square frame (`aspect-square`, `overflow-y-auto`) with stacked download rows |

Each attachment row:

- Standard card link, `flex items-center gap-3`
- Leading: icon chip `FileText`
- Body: original filename (truncated) + file size in mono uppercase
- Trailing: `ExternalLink` @14px
- Action: download file via authenticated monitor route

**Empty state (no files):**

- Centered column, 40% opacity
- `FileText` @28px + **NO FILES** (mono `10px`)

### Links

| Label | **LINKS** — mono `10px`, `mutedForeground`, `mb-2` |
| List | Stacked cards, `space-y-2` |

Each link row (prototype shows disabled placeholders):

- Standard card, `flex items-center gap-3`
- Leading: icon chip `ExternalLink`
- Title: DM Sans `14px`, `foreground`
- Trailing: `ExternalLink` @14px
- Prototype: `cursor-not-allowed opacity-60` — **User Manual**, **Maintenance Manual**

Footer hint when empty: **NO LINKS ADDED** (mono `10px`, `mutedForeground`, `mt-2`)

**Production:** render actual URLs from sync; open in new tab when present.

---

## Divider

Full-width `border-t border-border`

---

## Service log entry row

Tappable card (same hover as dashboard vehicle rows):

| Zone | Content |
|------|---------|
| Leading | Icon chip `Wrench`, amber |
| Body | **SERVICE LOG** (mono `10px`, `mutedForeground`) + `{n} entries recorded` (DM Sans `14px`) |
| Trailing | `ChevronRight` @16px |

Click → [Service Log](service-log.md) for this vehicle.

---

## Data model (prototype)

```text
Vehicle {
  id, name, mileage, unit, entryCount,
  description
}
```

---

## Livewire / Blade notes

- Route: `monitor.vehicles.{vehicle}` with authorized session scope
- Image: inline data URI built from synced raw base64 (`maintenance_schedule_image`)
- Links: optional collection; hide section footer when links exist
- Back: `wire:navigate` to dashboard or browser history
