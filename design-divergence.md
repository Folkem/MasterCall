# Design Divergence Guide
This project must look and feel clearly distinct from the **prior project (FitFlow)**.
FitFlow's signature look was: a fixed left sidebar, a warm amber/orange palette on stone neutrals,
a rounded "friendly" font (Nunito), flat bordered cards, pill buttons, Heroicons, and bottom-right
toasts. Deliberately avoid all of that. For each pattern below, follow the **USE INSTEAD** line.

The target feel for MasterCall: a clean, trustworthy "trades & utilities" product — cool palette,
structural typography, a touch of hi-vis accent. Not playful, not warm.

## Layout
- AVOID: a fixed left sidebar for primary navigation (FitFlow's signature).
- USE INSTEAD: a sticky **top header** with the brand + primary nav and a slim utility row
  (auth/account links). Client/master/admin areas navigate via **horizontal tabs**, not a side rail.
  Content sits in a generous centered container.

## Color palette
- AVOID: amber/orange as the brand color; stone/zinc neutrals; warm backgrounds.
- USE INSTEAD: a cool palette — **teal** (`teal-600`/`teal-700`) as the primary action color,
  **slate** for neutrals, `slate-900` for dark surfaces, page background `slate-50`. A single
  hi-vis accent — `yellow-400` (or `lime-400`) — used sparingly for key CTAs and "доступний" badges.

## Typography
- AVOID: Nunito / rounded-friendly fonts; the default system stack.
- USE INSTEAD: a structural pairing — **Space Grotesk** (or Sora) for headings, **Inter** for body
  (imported via `@import` in `app.css` or a `<link>`). Use deliberate weight contrast; reserve
  `tracking-tight` for large headings only.

## Cards & surfaces
- AVOID: FitFlow's flat `border border-stone-200` no-shadow cards.
- USE INSTEAD: **elevated** white cards — `rounded-xl shadow-sm hover:shadow-md transition` — with a
  thin **category-colored top accent strip**. Subtle lift on hover for clickable cards.

## Buttons
- AVOID: pill-shaped (`rounded-full`) buttons; outlined destructive links.
- USE INSTEAD: **`rounded-lg` solid** buttons with clear primary (teal) / secondary (slate outline)
  hierarchy. Destructive actions are solid `bg-red-600`. Pair an icon with the label where it helps.

## Tables
- AVOID: FitFlow's borderless alternating-row tables with a colored sticky thead.
- USE INSTEAD: **divided rows** (`divide-y divide-slate-200`) on white, a plain light header
  (`bg-slate-100 text-slate-600 uppercase text-xs`), `hover:bg-slate-50`, and **status pills** in
  the status column.

## Icons
- AVOID: emoji as UI icons; reusing Heroicons.
- USE INSTEAD: the **Lucide** icon set, as inline SVGs wrapped in small Blade components.

## Status badges
- Give each order status a distinct pill color (e.g., pending=slate, accepted=teal,
  confirmed=blue, in_progress=amber, completed=emerald, declined=red, cancelled=slate-400).
  Define them once (a Blade component or an Enum method) and reuse everywhere.

## Master profile, gallery & ratings
- Portfolio gallery: a responsive tile grid (`grid-cols-2 md:grid-cols-3 lg:grid-cols-4`, `gap-3`,
  `rounded-lg overflow-hidden aspect-square object-cover`), optional lightbox via Alpine.
- Ratings: filled/empty star icons (Lucide `star`) in the teal/yellow accent — not a numeric label alone.
- Service price: show the `price_type` as a Ukrainian prefix/suffix ("від 350 грн", "350 грн/год").

## Flash messages
- AVOID: FitFlow's bottom-right corner toasts; plain `bg-green-100` inline banners.
- USE INSTEAD: a **top-center slide-down toast** that auto-dismisses, with a left color bar and a
  Lucide status icon.

## Admin panel
- AVOID: a collapsible left admin sidebar (FitFlow) and a dark horizontal `bg-gray-900` navbar.
- USE INSTEAD: a top admin bar with **section tabs** and an active-state underline, over a
  **dashboard built from stat cards** in a responsive grid.
