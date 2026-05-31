# MasterCall — Implementation Spec

Build in this order. Commit after each module. Verify each module before moving on (see end).
Also obey the **Cross-cutting requirements** section — those apply to EVERY module and are the
things most often forgotten.

## 1. Foundation
Fresh Laravel install. Single `users` table with `role` enum (client/master/admin). Auth scaffolded
manually — hand-rolled register / login / logout controllers + Blade views on the default session
guard. No Breeze/Jetstream/Fortify or any other starter kit. Blade layouts: `public`,
`account` (client), `cabinet` (master), `admin`. Middleware **`role:master`**, **`role:admin`**
(an alias-registered middleware that 403s on role mismatch). Deactivated users (`is_active = false`)
cannot log in — fail login with a Ukrainian message.

## 2. Database migrations and seeders
- **users**: name, email (unique), password, phone (nullable), role (string), is_active (bool, default true), timestamps
- **service_categories**: name, slug (unique), icon (Lucide icon name, string), description (nullable)
- **master_profiles**: user_id, bio (nullable), photo_path (nullable — main photo on the `public` disk),
  city, years_experience (int, default 0), is_available (bool, default true), timestamps
- **category_master_profile** (pivot): category_id, master_profile_id — **a master works in one OR MORE
  categories** (many-to-many; "майстер на всі руки" spans several). Used for list filtering.
- **master_photos**: master_profile_id, photo_path, sort_order (int) — portfolio gallery of past work
- **services**: master_id (→ users), category_id, name, description (nullable), price (decimal 10,2),
  price_type (string enum: fixed / from / hourly), duration_minutes (int, nullable), timestamps
- **bookings**: client_id, master_id, service_id (nullable — null = custom request), category_id,
  address (string), scheduled_at (datetime), description (text), price (decimal 10,2, nullable),
  status (string enum), stripe_session_id (nullable), master_note (nullable), started_at (nullable),
  completed_at (nullable), timestamps. Index (master_id, status) and (client_id, status).
- **work_reports**: booking_id, master_id, client_id, content (text), timestamps
- **reviews**: client_id, master_id, rating (tinyint 1-5), comment (nullable), timestamps; UNIQUE(client_id, master_id)
- **messages**: booking_id, sender_id (→ users), body (text), read_at (nullable), timestamps — per-order chat
- **favorites**: client_id, master_id, timestamps; UNIQUE(client_id, master_id)
- **notifications**: Laravel's built-in `notifications` table (`php artisan notifications:table`, database channel)

**Seed** (idempotent, all seeded users share password `password`):
- The fixed ~6 service_categories with real Lucide icon names (e.g. `wrench`, `zap`, `paint-roller`/`brush`, `hammer`, `sparkles`, `plug`).
- ~10 masters: profile, 1-3 categories each, 2-4 services each, 0-4 gallery photos (placeholder or null).
- ~25 clients.
- ~45 bookings spread across **all** statuses — include several `completed` (so reviews/reports exist)
  and some `confirmed`/`in_progress` (so cabinets aren't empty).
- ~15 reviews, ~10 work_reports, a few message threads on active orders, a handful of favorites,
  and a couple of unread notifications so the bell isn't empty.
- 1 admin with documented credentials: **admin@example.com / password**. All seeded emails use the `example.com` domain.
Use a placeholder image (or null) for photos. Seeders must run cleanly under `migrate:fresh --seed --step`.

## 3. Public site
- **Homepage**: hero with a search field (free text → masters list), a grid of service categories
  (Lucide icon + name, link to the masters list filtered by that category), and a "топ майстрів" strip
  (highest average rating, with photo + rating).
- **Masters list** (`/masters`): cards with photo (fallback placeholder if null), name, categories,
  city, average rating + review count, and "від {min service price} грн".
  - **Filter**: by category (slug) and by city. **Sort**: rating (default), price asc, price desc.
  - **Search**: free-text over master name (and optionally service names).
  - **Paginate** (~12 per page); keep filter/sort/search in the query string across pages.
  - Empty state: a friendly Ukrainian "Майстрів не знайдено" block.
- **Single master page** (`/masters/{master}`): bio, categories, city, years of experience, the
  **gallery** of work photos, the list of services (name, price + price_type label, duration),
  average rating + the reviews list (client name, stars, comment, date), and a "Замовити" CTA per
  service (and a general one). 404 if the user isn't an active master.

## 4. Client booking flow
- Register / login (client role by default). Repopulate old input + show field errors on failure.
- From a master page (optionally pre-selecting a service), request an order: enter address, a future
  `scheduled_at` (date+time), and a job description. No availability validation — any future datetime
  is allowed. Pre-fill `price` from the chosen service (master may revise it on acceptance).
- `/account/orders` — list own orders, newest first, with status badges; **filter by status** (tabs or
  select). **Cancel** pending/accepted/confirmed orders, only before `scheduled_at`.
- `/account/orders/{order}` — detail (ownership-checked): status, master, service, address,
  scheduled_at, price, master_note, and the work report once completed. Shows **Сплатити** when
  `accepted`, and **Залишити відгук** when `completed`.

## 5. Payments — Stripe
- Pay for an **accepted** order: "Сплатити" → Stripe Checkout (UAH, line item = order's service/price,
  `client_reference_id` = order id) → success marks the order `confirmed`.
- `/checkout/success?session_id=...` verifies the session belongs to this order and is paid, then
  confirms it; `/checkout/cancel` returns the user to the order with an info message.
- Persist `stripe_session_id`. **Guard**: only `accepted` orders are payable; never re-confirm an
  already-confirmed order; verify the order belongs to the authenticated client.
- Keep all Stripe calls in a `PaymentService`; structure for a clean future webhook addition.

## 6. Master cabinet
Under `/cabinet/*` with `role:master`. Every action verifies the record belongs to this master.
- **Profile**: edit bio, city, years_experience; pick categories (multi-select); upload/replace the
  main photo; manage the gallery (add photos, delete a photo); toggle `is_available`.
- **Services**: CRUD own services (name, description, price, price_type, duration, category).
- **Requests / orders list**: incoming orders, filterable by status. Actions by status:
  - pending → **accept** (set/confirm final `price`, optional note) → `accepted`; or **decline**
    (requires `master_note`) → `declined`.
  - confirmed → **start** → `in_progress` (set `started_at`).
  - in_progress → **complete** → `completed` (set `completed_at`).
  - accepted/confirmed → **decline** with note still allowed before work starts.
  - All transitions go through a `BookingService` method that validates the current status.
- **Work report**: after `completed`, add/edit a report (content) shown to the client.

## 7. Reviews
- A client leaves/edits exactly one review per master, only with ≥1 `completed` order with that master.
  Enforce in a Form Request + Service (uniqueness via `updateOrCreate`, and the "completed order exists"
  rule). Show the edit form pre-filled if a review already exists.
- Review = rating 1-5 (star input) + comment.
- Master average rating (rounded to 1 decimal) and count, shown on the public profile and masters list.

## 8. Admin panel
Under `/admin/*` with `role:admin`.
- **Masters**: create master users + profiles (incl. categories multi-select, main photo + gallery
  upload), edit, list with **search** by name/email. Delete a gallery photo.
- **Clients**: list with search; deactivate/reactivate (`is_active`).
- **Service categories**: CRUD (name, slug, icon, description).
- **Orders**: list all, **filter by status** + search by client/master; change status manually if needed.
- **Dashboard** (stat cards): orders today, revenue this month (sum of `price` for confirmed+ orders),
  total masters/clients, top masters by completed orders.

## 9. Chat, notifications & account extras
- **In-order chat**: a message thread tied to a booking, between its client and its master. Shown on
  both the client order detail (`/account/orders/{order}`) and the master order detail. Post via a
  `messages.store` route; both participants (and only they) may read/post — verify ownership.
  Display sender, body, time; mark the other side's messages `read_at` when viewed. Newest at the
  bottom; light Alpine polling (every ~10s) to refresh is optional, plain reload is acceptable.
- **In-app notifications**: Laravel database notifications + a bell in the top nav showing the
  unread count and a dropdown of recent items. Notify on: order accepted/declined/completed (→ client),
  new order request (→ master), new chat message (→ the other participant). Mark-as-read on open;
  "позначити всі прочитаними". Each links to the relevant order.
- **Favorites**: a client can add/remove a master to favorites (toggle on master cards + profile);
  `/account/favorites` lists saved masters. Enforce UNIQUE(client_id, master_id).
- **Client profile**: `/account/profile` — edit own name, phone, and password (current-password check
  to change it). Validate + repopulate as usual.
- **Static pages**: public `Про нас` (`/about`), `Питання` (`/faq`), `Контакти` (`/contacts`), linked
  from the footer. Plain Blade content, no DB.

## Cross-cutting requirements (apply to EVERY module — these are the usually-missed ones)
- **Authorization / ownership**: every `/account/*` and `/cabinet/*` action must confirm the record
  belongs to the authenticated user (`abort_unless(...)`/policy); never trust the route id alone (no IDOR).
- **Status guards**: never perform a transition/payment/review/cancel that the current status disallows;
  guard server-side, not just by hiding buttons.
- **Filtering / sorting / search / pagination**: any list that can grow (masters, orders, admin tables,
  reviews) supports the filters above, paginates, and preserves query params across pages.
- **Empty states**: every list renders a friendly Ukrainian message when empty — never a blank page.
- **Forms**: always `@csrf` (+ `@method` for PUT/PATCH/DELETE), repopulate with `old()`, and show
  per-field errors via `@error`. Validation messages in Ukrainian (Form Request `messages()`).
- **Images**: validate `['nullable','image','max:2048']` (gallery items `['image','max:2048']`);
  store with `->store('masters'|'gallery','public')`; show a placeholder when `photo_path` is null;
  on replace/delete, remove the old file from the `public` disk (`Storage::disk('public')->delete`).
- **Money**: `decimal:2` cast; display formatted in UAH (e.g. "350 грн"); render the `price_type`
  as a Ukrainian label ("фіксована" / "від" / "за годину").
- **Dates**: cast datetimes; display in a consistent Ukrainian-friendly format; compare against `now()`.
- **Eager loading**: load relationships with `with()` to avoid N+1 on every list/detail page.
- **Flash + errors**: success/error via the top-center toast (see design-divergence); validation errors
  inline on the form.
- **Responsive nav**: the top header collapses to a mobile menu (Alpine `x-data` toggle).

## Verification per module
After each module:
- `php artisan route:list` — new routes present
- `curl -sI` to one key route per module — 200 or expected redirect
- `php artisan migrate:fresh --seed --step` — completes cleanly
- `./vendor/bin/pint --dirty` — no issues
- Spot-check one happy path and one guarded/forbidden path returns the right status
- Always commit after each individual module
