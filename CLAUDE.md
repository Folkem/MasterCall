# MasterCall — «Виклик майстра» — Diploma Project
Online booking platform for home-service masters. Clients browse a catalog of services by
category (plumber, electrician, painter, handyman, etc.), find a master, and request a visit at
a proposed date/time. Masters accept or decline requests, set the final price, do the work and
mark it complete. Admin manages users, categories, orders, and sees statistics.

## Stack
PHP 8.4, Laravel 13, MySQL, Blade, TailwindCSS
Icons: Lucide (SVG, via Blade components — no emoji)
Payments: stripe/stripe-php, Checkout Sessions (test mode)
No FullCalendar, no SPA framework — server-rendered Blade.
**Alpine.js (installed npm package) is allowed** for light interactivity only: the flash toast auto-dismiss,
dropdowns, the mobile-nav toggle, the star-rating input, image previews, the notification-bell
dropdown, and optional chat polling. Not for app logic.

## Goal
See @goal.md

## Spec
See @spec.md

## Conventions
See @coding-conventions.md

## Roles
Single `users` table with `role` enum: `client`, `master`, `admin`.
Single session guard; authorization via middleware (`role:master`, `role:admin`) —
register a `role` middleware alias that 403s on mismatch.
`is_active` flag — admin deactivates a user; deactivated users are blocked from logging in.
Beyond role middleware, every per-record action must verify **ownership** (no IDOR).

## Domain glossary (use these terms consistently)
- **Master (майстер)** — a service provider with a profile, a primary category, and a list of services.
- **Service category / спеціальність** — fixed catalog: Сантехніка, Електрика, Малярні роботи,
  Майстер на всі руки, тощо. Each has a Lucide icon and a slug. A master works in **one or more**
  categories (many-to-many); each service belongs to one category.
- **Gallery (портфоліо)** — a master has a main photo plus a gallery of past-work photos (with sort order).
- **Service (послуга)** — a concrete offering by one master: name, description, price, price_type.
- **Order / Booking (замовлення)** — a client's request for a master to perform a service at an
  address and date/time. Carries the status flow below.
- **Work report (звіт про роботу)** — note the master writes after completing an order, visible to the client.
- **Review (відгук)** — one rating+comment per client per master, allowed only after a completed order.
- **Message (повідомлення)** — a chat message tied to one booking, between its client and master.
- **Notification (сповіщення)** — in-app database notification (bell) on order/chat events.
- **Favorite (обране)** — a master saved by a client for quick access.

## Scheduling model
Free date/time requests — NO recurring availability windows, NO calendar widget.
The client proposes `scheduled_at` (a datetime) + address + a description of the job.
The master decides whether they can take it (accept/decline). Keep it that simple.

## Order status flow
- pending → (master accepts, sets final `price` + optional note) **accepted**
- accepted → (client pays via Stripe Checkout) **confirmed**
- confirmed → (master starts the job) **in_progress** (sets `started_at`)
- in_progress → (master finishes) **completed** (sets `completed_at`)
- pending / accepted / confirmed → **declined** (master, requires `master_note`)
- pending / accepted / confirmed → **cancelled** (client, only before `scheduled_at`)
- After **completed**: client may leave/edit a review; master may add/edit a work report.

## Payments — Stripe sandbox
- stripe/stripe-php, test keys in .env (STRIPE_KEY, STRIPE_SECRET).
- Currency: UAH for Checkout and all displayed prices (test card 4242 4242 4242 4242 works in any currency).
- Single checkout entry point: paying for an **accepted** order.
- Payment-after-acceptance by design (avoids refund logic) — an order is only paid once the master
  has accepted it and set the final price.
- No webhooks for now — the order is marked `confirmed` when the user returns to the success route
  with a valid `session_id`. Real webhook integration is future work; structure code so it's a clean addition.

## Design divergence
See @design-divergence.md — this project must look and feel visually distinct from the prior
project (FitFlow). Follow the "USE INSTEAD" guidance, not just the avoidances.

## Commands
- `composer install`
- `npm install && npm run build` (compiles TailwindCSS via Vite — required before serving)
- `php artisan migrate:fresh --seed --step`
- `php artisan storage:link` (publishes uploaded master photos on the public disk)
- `./vendor/bin/pint --dirty`
- `php artisan route:list`
