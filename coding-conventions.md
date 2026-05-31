## Typing
- Always type-hint parameters, returns, and properties. Use union types where applicable.
- Use `mixed` only when the type is genuinely dynamic (e.g., decoded JSON of unknown shape).
  Use PHPDoc `@param array<string, mixed>` for typed arrays.

## Imports
- Always `use` classes at the top of the file.
- Never use fully-qualified class names inline (`\App\Models\User`).

## Code style
- Use `./vendor/bin/pint` (Laravel Pint) for code formatting after you're done generating code.
- Never format code by hand. Always run Laravel Pint for it.

## Architecture
- Resourceful controllers, kept thin. Business logic lives in Services (domain- and role-specific)
  under `app/Services/` — e.g., `BookingService`, `PaymentService`, `ReviewService`.
- Validation via Form Requests (`app/Http/Requests/`).
- Use the Eloquent ORM, no raw queries.
- Keep the status-flow transitions in one place (a Service / model method), not scattered across controllers.

## Models
- Use PHPDoc for documenting model properties (both table columns and relationship-derived ones).
- Always define `$fillable`. Define `casts()` when applicable (datetimes, decimals, booleans, enums).
- Always define the relationships.

## Enums
- Use PHP Enum classes for any property with a fixed set of values (order status, role, price_type).
- Do NOT use database-level enums — store as `string` columns; cast to the Enum in the model.

## Authorization & data access
- Role gating via the `role` middleware; **ownership** via `abort_unless(...)` or a Policy on
  every per-record action (a master/client may only touch their own records). Never trust the route id.
- Enforce status transitions server-side (in the Service), regardless of which buttons the view shows.
- Always eager-load relationships with `with()` on lists and detail pages to avoid N+1 queries.
- Lists that can grow use `->paginate(...)` and preserve filter/sort/search via `withQueryString()`.

## Files & images
- Validate uploads: `['nullable','image','max:2048']` (gallery items `['image','max:2048']`).
- Store on the `public` disk: `$file->store('masters'|'gallery','public')`; render via `Storage::url()`
  with a placeholder fallback when the path is null. On replace/delete, delete the old file.

## Frontend
- Blade + TailwindCSS. No SPA framework. **Alpine.js via installed npm package is allowed** for light interactivity
  (toast auto-dismiss, dropdowns, mobile-nav toggle, star-rating input, image preview) — not app logic.
- Forms: always `@csrf` (+ `@method` for PUT/PATCH/DELETE), repopulate inputs with `old()`, and show
  per-field validation errors via `@error`. Render an empty-state block for every list.
- Inline TailwindCSS classes. No per-component CSS files. Web fonts allowed — via a `<link>` in the
  layout `<head>`, or `@import`/`@font-face` in the single `resources/css/app.css` Vite entry.
- Use layouts in `resources/views/layouts`, extended via `@extends()` / `@section()`.
- Icons via reusable Blade components wrapping inline Lucide SVGs (no emoji as UI icons).

## Localization
- Code, comments, commit messages: English.
- All user-facing strings: Ukrainian, hardcoded. This includes page copy, labels, buttons,
  navigation, validation messages (in Form Requests via a `messages()` override), and the admin UI.
- No Laravel lang files — write Ukrainian directly in Blade and in `messages()` arrays.
- Keep terminology consistent with the domain glossary in CLAUDE.md (майстер, послуга, замовлення, відгук).
