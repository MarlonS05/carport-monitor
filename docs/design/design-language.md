# Industrial Dark — Design Language

> A portable, framework-agnostic design language extracted from the Carport
> app. It defines the tokens, type system, components, and interaction rules
> that give the product its "workshop-grade instrument panel" feel: a near-black
> canvas, a single warm amber accent, condensed display type, and monospace
> labels. Adopt it wholesale or cherry-pick the token tables.
>
> Every value below is a **design token**. Consuming projects should mirror this
> file into their own token layer (CSS variables, a theme object, a Tailwind
> config, a Flutter `ThemeExtension`, etc.) rather than hardcoding literals.
>
> **Screen layouts and feature UI** for the web Monitor are documented separately
> in [ui-spec/](ui-spec/README.md) (extracted from the Figma Make prototype).

---

## 1. Design principles

1. **One accent, used sparingly.** A single warm amber (`#E87C2A`) is the only
   chromatic color. Everything else is neutral. The accent marks the primary
   action, the current selection, active icons, and eyebrow labels — nothing
   else. If two things on a screen are amber, question one of them.
2. **Dark, layered neutrals.** The UI is built from four flat greys stacked by
   elevation (background → card → secondary → input). No gradients, no drop
   shadows except the floating action button.
3. **Type carries the personality.** Condensed bold display type for titles,
   monospace for labels/metadata/stats, humanist sans for body. Labels are
   `UPPERCASED` with wide tracking to read like gauge stencils.
4. **Hairline borders over shadows.** Separation comes from 1px translucent-white
   borders and background contrast, not elevation.
5. **Immediate, physical feedback.** Everything tappable animates on press
   (color shift, border warm-up, or scale). Motion is short (150–200ms) and
   functional, never decorative.
6. **Calm, roomy layout.** Generous vertical rhythm, a single readable column
   capped at ~384px, and consistent 12/16/20 spacing steps.

---

## 2. Color tokens

Values are the source of truth. Names are semantic — reference the name, not the
hex, in components.

| Token | Hex / RGBA | Role |
|-------|-----------|------|
| `background` | `#111111` | App canvas / scaffold |
| `foreground` | `#F0EDE8` | Primary text on background/card (warm off-white) |
| `card` | `#1C1C1E` | Card & elevated surface |
| `cardForeground` | `#F0EDE8` | Text on cards |
| `primary` | `#E87C2A` | Accent: CTAs, selection, active icons, eyebrows |
| `primaryForeground` | `#111111` | Text/icons on top of `primary` |
| `primaryHover` | `#F59E0B` | Pressed/hover state of primary surfaces |
| `secondary` | `#2A2A2C` | Pressed row bg, icon chips, back button |
| `secondaryForeground` | `#F0EDE8` | Text on secondary |
| `muted` | `#2A2A2C` | Muted surface (same as secondary) |
| `mutedForeground` | `#888884` | Secondary text, metadata, inactive icons |
| `border` | `rgba(255,255,255,0.08)` (`#14FFFFFF`) | Hairline borders |
| `inputBackground` | `#242426` | Text-field fill |
| `destructive` | `#D4183D` | Delete/error text, borders, dialogs |
| `success` | `#34D399` | Positive status (granted, healthy) |
| `ring` | `#E87C2A` | Focus ring on inputs (== `primary`) |

### 2.1 Accent alpha tokens

Selected/pressed states tint or outline with the accent at fixed opacities
rather than introducing new colors.

| Token | Value | Use |
|-------|-------|-----|
| `primaryTint` | `0.12` | Fill behind a **selected** card/option |
| `primaryBorder` | `0.5` | Border of a **selected** card/option |
| `primaryHoverBorder` | `0.4` | Border of an **unselected but pressed** card |

### 2.2 Status color mapping

| State | Color |
|-------|-------|
| Positive / allowed / healthy | `success` |
| Negative / blocked / error | `destructive` |
| Neutral / unknown / pending | `mutedForeground` |

---

## 3. Typography

Three families, each with a distinct job. Substitute equivalents if these
aren't available, but keep the **condensed / mono / sans** split.

| Family | Role | Reference font |
|--------|------|----------------|
| Condensed display | Titles, hero, tile & list titles, primary CTA | **Barlow Condensed** |
| Monospace | Eyebrows, field labels, metadata, stats, pills | **DM Mono** |
| Humanist sans | Body copy, input text, helper text | **DM Sans** |

### 3.1 Type scale

Letter-spacing is expressed in **em** (multiply by font size for absolute px).
`weight` uses standard 100–900. `line` is the line-height multiplier.

| Style | Family | Size | Weight | Tracking (em) | Line | Color | Notes |
|-------|--------|-----:|-------:|--------------:|-----:|-------|-------|
| Hero eyebrow | Mono | 12 | 500 | 0.10 | 1.2 | `primary` | UPPERCASE |
| Hero line 1 | Condensed | 36 | 700 | 0.025 | 1.1 | `foreground` | UPPERCASE |
| Hero line 2 | Condensed | 36 | 500 | 0.025 | 1.1 | `mutedForeground` | UPPERCASE |
| Screen title | Condensed | 24 | 700 | 0.025 | 1.2 | `foreground` | |
| Tile title | Condensed | 18 | 700 | 0.025 | 1.2 | `foreground` | |
| List title | Condensed | 16 | 700 | 0.025 | 1.2 | `foreground` | |
| Tile subtitle | Mono | 12 | 400 | 0 | 1.4 | `mutedForeground` | 70% `primaryForeground` on accent |
| Field label | Mono | 12 | 500 | 0.10 | 1.4 | `mutedForeground` | UPPERCASE |
| List meta | Mono | 12 | 400 | 0 | 1.4 | `mutedForeground` | |
| Footer / stat | Mono | 12 | 400 | 0.10 | 1.4 | `mutedForeground` | UPPERCASE, centered |
| Primary CTA | Condensed | 16 | 700 | 0.10 | 1.2 | `primaryForeground` | |
| Body | Sans | 14 | 400 | 0 | 1.5 | `mutedForeground` or `foreground` | |
| Pill / tag | Mono | 12 | 400 | 0.10 | 1.2 | `foreground` | |

### 3.2 Casing rule

Eyebrows, field labels, section labels, footer stats, empty-state messages, and
hero titles are **UPPERCASE**. Do this at render time (transform the string), not
in stored data.

---

## 4. Spacing, radius, size

### 4.1 Spacing scale

| Token | px | Use |
|-------|---:|-----|
| `screenH` | 20 | Horizontal screen padding (left/right gutter) |
| `section` | 16 | Between major sections; card inner padding |
| `list` | 12 | Gap between stacked cards/rows; grid gutter |
| `grid` | 12 | Grid gutter |
| `labelGap` | 6 | Between a field label and its input |
| `topBarTop` | 24 | Top padding of the top bar |
| `topBarBottom` | 16 | Bottom padding of the top bar |
| `homeTop` | 40 | Top padding of a dashboard/home hero |

### 4.2 Radius

| Token | px | Use |
|-------|---:|-----|
| `card` | 12 | Cards, tiles, primary/destructive buttons |
| `input` | 8 | Text fields |
| `iconBox` | 8 | Square icon chips |

### 4.3 Sizing

| Token | px | Use |
|-------|---:|-----|
| `maxWidth` | 384 | Max content column width (center on wider screens) |
| `minTap` | 48 | Minimum tap target height |
| `fab` | 56 | Floating action button diameter |
| `backButton` | 36 | Circular back button diameter |
| Icon chip | 44 | Leading icon container in list cards |

---

## 5. Iconography

- **Library:** [Lucide](https://lucide.dev) (thin, consistent line icons). Any
  single-weight outline icon set works as a substitute.
- **Sizes:** 28 (dashboard tile), 24 (FAB), 20 (list-card leading icon, hub
  rows), 18 (back chevron), 16 (chevrons, inline status), 14 (input prefix), 48
  (empty-state).
- **Color:** `primary` when active/leading, `mutedForeground` when inactive,
  `primaryForeground` on accent surfaces. Empty-state icons use
  `mutedForeground` at 30% opacity.

---

## 6. Motion

| Token | Duration | Use |
|-------|---------:|-----|
| `standard` | 200ms | Press/hover color, border, and background transitions |
| `fab` | 150ms | FAB scale on press/hover |

Interaction feedback patterns:

- **Pressable surface:** on press, background lifts one layer (`card` →
  `secondary`) and/or the border warms toward the accent (`border` →
  `primary @ primaryHoverBorder`).
- **Primary button:** background `primary` → `primaryHover` on press.
- **FAB:** scale `1.0` → `0.95` on press, `1.05` on hover.
- **Focus:** input border snaps to `ring` (accent), no transition needed.

No easing curve is prescribed beyond the platform default; keep it standard/ease.

---

## 7. Layout

- **Shell:** dark scaffold, content centered and constrained to `maxWidth`
  (384). Respect safe areas.
- **Vertical structure:** Top bar → scrollable content with `screenH` horizontal
  padding → optional footer stat. Dashboards add a hero block at the top.
- **Stacking:** cards/rows are separated by `list` (12) gaps, not dividers.
- **Grid:** dashboard uses a 2-column grid with `grid` (12) gutters; tiles are
  square-ish with icon top-left and title/subtitle bottom-left.

---

## 8. Component recipes

Each recipe lists the tokens that define it so it can be rebuilt on any stack.

### 8.1 Top bar

Row: optional circular **back button** (36, `secondary` fill, chevron-left,
icon `mutedForeground` → `foreground` on press, 35% opacity when disabled) +
screen title (Screen title style, max 2 lines, ellipsis) + optional trailing
action. Padding: `screenH` sides, `topBarTop` top, `topBarBottom` bottom.

### 8.2 List card (tappable row)

`card` bg, 1px `border`, radius `card`, padding 16. Contents: 44px icon chip
(`secondary` fill, radius `iconBox`, `primary` icon @20) + title (List title) +
optional subtitle (List meta, 2px below) + trailing chevron-right @16
(`mutedForeground`, → `primary` when pressed). Pressed: bg → `secondary`, border
→ `primary @ primaryHoverBorder`. Min height `minTap`.

### 8.3 Dashboard hero

Eyebrow (Hero eyebrow, UPPERCASE) + two-line title (Hero line 1 bold
`foreground`, Hero line 2 medium `mutedForeground`), both UPPERCASE. Padding:
`screenH` sides, `homeTop` top, 24 bottom, 8 between eyebrow and title.

### 8.4 Dashboard tile

Square card in a 2-col grid. **Default:** `card` bg, `border`, `primary` icon,
`foreground` title. **Accent:** `primary` bg, `primary` border,
`primaryForeground` icon+title, subtitle at 70% `primaryForeground`. Icon @28
top-left; title (Tile title) + subtitle (Tile subtitle) bottom-left. Pressed:
accent → `primaryHover`; default → `secondary` bg + `primary @
primaryHoverBorder` border.

### 8.5 Primary button (CTA)

Full-width, `primary` bg, radius `card`, 16 vertical padding, centered Primary
CTA text (`primaryForeground`). Pressed → `primaryHover`. Disabled → `primary`
at 50%. Loading → centered 20px spinner in `primaryForeground`.

### 8.6 Destructive button

Full-width **outlined**: transparent fill, 1px `destructive` border, radius
`card`, Body text in `destructive`. Pressed → border `destructive @ 0.8`.
Disabled → `destructive @ 0.5` for border and text. Loading → `destructive`
spinner.

### 8.7 Input field

Label (Field label, UPPERCASE) above, `labelGap` (6) gap, then a box: `inputBackground`
fill, radius `input`, 1px border. Border color: `border` at rest → `ring`
focused → `destructive` on error. Optional leading icon @14 `mutedForeground`.
Body text, `primary` cursor. Error text (List meta in `destructive`) 6 below.

### 8.8 Radio / option card

List-card shape. **Selected:** `primary @ primaryTint` fill, `primary @
primaryBorder` border, `primary` title, trailing filled radio. **Unselected
pressed:** `secondary` fill, `primary @ primaryHoverBorder` border.

### 8.9 Status card

Read-only card: 36×36 icon chip + title + status label colored per §2.2
(`success` / `destructive` / `mutedForeground`).

### 8.10 Section label

UPPERCASE mono heading (Field label style) above a group of cards, `list` (12)
gap below.

### 8.11 Footer stat

Centered, UPPERCASED mono line (Footer/stat style), 24 vertical padding.

### 8.12 Empty state

Centered column: large icon (48, `mutedForeground` @30%) + UPPERCASE mono
message (16 below).

### 8.13 Floating action button

56px circle, `primary` fill, `primaryForeground` icon @24 (typically plus),
elevation 6. Scale animation per §6. Offset 20 right / 24 bottom.

---

## 9. Accessibility

- Tap targets ≥ `minTap` (48px).
- Provide semantic button/label roles on all custom pressables and icon-only
  controls (back button, FAB, chevron rows).
- Disabled controls drop to ~35–50% opacity **and** are non-interactive.
- Body text minimum 14; labels/meta minimum 12. Never encode meaning in color
  alone — pair status color with a label (§2.2).

---

## 10. Adoption checklist

```
[ ] Mirror §2 color tokens into your theme layer (semantic names, not hex).
[ ] Add §2.1 accent-alpha and §2.2 status mappings.
[ ] Load the three type families (§3) or chosen equivalents; encode §3.1 scale.
[ ] Add §4 spacing / radius / size tokens.
[ ] Adopt an outline icon set (§5) and the size ramp.
[ ] Define the two motion durations (§6) and press-feedback patterns.
[ ] Build the shared components in §8 from tokens (no literals).
[ ] Verify §9 accessibility rules.
```

---

## Appendix — Flutter mapping (source project)

The reference implementation lives in `lib/theme/garage_theme.dart`:

- Color tokens → `GarageColors` + the `GarageTheme` `ThemeExtension`.
- Accent alphas → `GarageAlpha`.
- Spacing / radius / size / motion → `GarageSpacing`, `GarageRadius`,
  `GarageSize`, `GarageMotion`.
- Type scale → `GarageTextStyles` (via `google_fonts`: `barlowCondensed`,
  `dmMono`, `dmSans`). Tracking helper: `size * em`.
- Components → `lib/screens/components/garage/` (`GarageShell`, `GarageTopBar`,
  `GarageListCard`, `GarageDashboardTile`, `GaragePrimaryButton`,
  `GarageInputField`, `GaragePressable`, etc.).
- Icons → `lucide_flutter`.

Other stacks: map the token tables to CSS custom properties + a utility layer
(web), a `Theme`/`Style` object (SwiftUI/Compose), or a Tailwind theme extension.
```
