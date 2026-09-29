# Connect Screen

> Pairing flow shown when no active mobile session. User scans a QR code from
> Carport mobile (**Settings → Web Monitor**).

---

## Purpose

1. Explain how to pair from the mobile app
2. Display a scannable QR code (session token)
3. Show pairing status (waiting / pairing / connected)

When pairing succeeds, the app navigates to the [Dashboard](dashboard.md).

---

## Layout

- Fills main content area, vertically centered (`flex-1 flex items-center justify-center`)
- Padding: `32px` (`p-8`)
- Inner: `max-w-3xl` grid — **1 column** on small screens, **2 columns** at `lg` with `gap-12`

```
┌─────────────────────────┬─────────────────────────┐
│  Copy + steps + CTA     │  QR card + status       │
│  (left column)          │  (right column)         │
└─────────────────────────┴─────────────────────────┘
```

---

## Left column — instructions

### Hero

| Element | Style |
|---------|--------|
| Eyebrow | **WEB MONITOR** — mono `11px`, `primary`, uppercase, wide tracking, `mb-3` |
| Title line 1 | **CONNECT** — Barlow Condensed `text-6xl`, extrabold, uppercase, `foreground` |
| Title line 2 | **YOUR GARAGE** — same size/weight, `mutedForeground` |

### Body copy

DM Sans `14px`, `mutedForeground`, relaxed line-height, `max-w-sm`.

> Open the Carport mobile app on your phone, then navigate to **Settings → Web Monitor**. Scan the QR code to the right to begin a live session.

Inline emphasis on path uses `foreground` + `font-medium`.

### Numbered steps

Three rows, `gap-3` vertical stack:

| Step | Number style | Text |
|------|--------------|------|
| 01 | mono `10px`, `primary`, fixed `w-5` | Open Carport on iOS or Android |
| 02 | same | Tap Settings → Web Monitor → Pair |
| 03 | same | Point your camera at the QR code |

Step text: DM Sans `13px`, `mutedForeground`.

### Simulate scan CTA (prototype only)

Primary button for development until real pairing is wired:

- Label: **SIMULATE SCAN** / loading **PAIRING…**
- Icon: `Wifi` @14px
- Style: `primary` bg, `primaryForeground` text, `rounded-lg`, `px-4 py-2.5`, hover darkens primary
- Disabled at 60% opacity while pairing
- Simulates 1.8s delay then calls `onConnect`

**Production:** replace with real WebSocket / polling pairing; remove or hide simulate button.

---

## Right column — QR card

### Card container

Standard card (`bg-card`, `border-border`, `rounded-xl`), `p-6`, `max-w-[280px]`, centered column, `gap-4`.

### QR code area

- Size: `192×192` (`w-48 h-48`)
- Prototype: inline SVG placeholder (amber corner squares + data dots)
- **Production:** render real QR encoding session URL/token

### Divider

Full-width `border-t border-border`

### Wordmark (below QR)

- **CARPORT** — Barlow Condensed `text-xl`, bold, uppercase, wide tracking, `foreground`
- **MONITOR** — same, `mutedForeground`

### Status row (below card)

| State | Indicator | Label |
|-------|-----------|-------|
| Waiting | `8×8` dot `mutedForeground` | **WAITING FOR SCAN…** |
| Pairing | `8×8` dot `primary`, pulse animation | **PAIRING…** |

Mono `10px`, label color matches state.

---

## States

| State | QR | Status text | Sidebar connection dot |
|-------|-----|-------------|------------------------|
| Idle | Static | WAITING FOR SCAN… | Grey |
| Pairing | Unchanged | PAIRING… (pulsing dot) | Grey |
| Success | — | Navigate away | Green |

---

## Data / backend (implementation)

- Generate unique session ID + QR payload URL
- Poll or subscribe for mobile scan confirmation
- On success: establish read-only sync channel, set `connected`, route to dashboard

---

## Accessibility

- QR image needs `alt` describing purpose (“Scan with Carport app to connect”)
- Steps are an ordered list semantically (`<ol>`)
- Pairing status should use `aria-live="polite"` for screen readers
