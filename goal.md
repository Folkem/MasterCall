# Goal

MasterCall («Виклик майстра») is fully implemented per CLAUDE.md and spec.md. Every module is
built, all routes return 2xx/3xx (no 500s), migrations run cleanly with
`php artisan migrate:fresh --seed`, and Stripe Checkout works end-to-end with test cards.

## Done means
- All 9 modules from spec.md are implemented
- `php artisan route:list` shows routes for all modules
- `php artisan migrate:fresh --seed --step` completes without errors
- `./vendor/bin/pint --dirty` reports no issues
- Every key route smoke-tested with curl returns 200 or expected redirect
- Ukrainian copy across all user-facing pages (written directly in Blade), consistent with the
  domain glossary in CLAUDE.md
- Homepage shows service categories with icons + search; masters list filters by category and city;
  single master page shows services, average rating, and the reviews list
- Order flow works: client requests → master accepts and sets price → client pays via Stripe →
  master marks in_progress then completed → client can review and see the work report
- Stripe Checkout completes with test card 4242 4242 4242 4242 when paying for an accepted order
- Master can accept/decline (decline requires a note), set the final price on acceptance, and
  manage their own services
- Client can leave a review only after a completed order; master average rating shows on profile and list
- Admin dashboard shows orders today, revenue this month (paid orders), and top masters by completed orders
- Per-order chat works between the order's client and master; messages mark as read
- Notification bell shows unread count and recent items (order events + new messages), mark-as-read works
- Client can favorite/unfavorite masters and edit own profile (name/phone/password)
- Public About / FAQ / Contacts pages render and are linked from the footer

## Hard limit
Stop after 110 turns regardless of completion status. Report what's done and what's not.
