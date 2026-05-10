# ClinicBook Agent Notes

## Stack And Commands
- Laravel 11 on PHP 8.2 (`composer.json`), with Vite 6 and Tailwind 3 for the frontend build.
- Install deps with `composer install` and `npm install`.
- Use `composer dev` for the normal local stack; it runs `php artisan serve`, `php artisan queue:listen --tries=1`, `php artisan pail --timeout=0`, and `npm run dev` together.
- Focused verification commands: `php artisan test`, `php artisan test --filter <Name>`, `vendor/bin/pint`, `npm run build`.
- There are no dedicated npm lint/typecheck scripts; do not guess them.

## Entry Points
- Public booking flow is wired from `routes/web.php` into `App\Http\Controllers\BookingController`.
- Dashboard/admin routes live in `routes/dashboard.php`.
- Keep booking rules in `app/Services/BookingService.php`; that file owns booking windows, capacities, duplicate checks, device/IP limits, and status payload shaping.

## Frontend Boundaries
- The public booking page does not use the Vite entry files for its behavior or styling. Its real UI lives in `resources/views/booking/index.blade.php`, `resources/views/booking/scripts.blade.php`, and `resources/views/booking/styles.blade.php`.
- `resources/js/app.js` and `resources/css/app.css` are minimal Vite stubs. Editing them will not change the booking page unless you also rewire the Blade view.
- Dashboard pages use Blade layouts under `resources/views/layouts` plus existing public assets; booking dashboard tables use Yajra DataTables server-side responses.

## Auth And Authorization Quirks
- Dashboard routes are protected by the custom `check.cookie` middleware, not plain auth middleware. It restores a session from the `user_id` cookie if present (`app/Http/Middleware/CheckUserCookie.php`).
- Fortify login redirects to `/dashboard`, uses `resources/views/auth/login.blade.php`, and authenticates by either `username` or `email` (`app/Providers/FortifyServiceProvider.php`).
- Authorization is role/policy based via `App\Policies\ModelPolicy`, with `super_admin` bypass in `AppServiceProvider`.

## Data Model Gotchas
- Clinic settings are a key/value table accessed through `ClinicSetting::getValue()` and `setValue()`; arrays are JSON-encoded strings in the database.
- Weekly and per-date clinic capacity/closure overrides are stored in `ClinicDayConfig`; the settings UI expects strabismus + other capacities to exactly equal the total capacity.
- `DatabaseSeeder` creates one fixed admin user directly. Treat seeding shared databases carefully.

## Testing And Env Notes
- `phpunit.xml` sets `APP_ENV=testing`, but the sqlite in-memory test DB lines are commented out. Tests will use whatever DB connection your test environment resolves to unless you override it.
- `.env.example` appears stale (`APP_NAME="TechNova - Point Of Sale"` and matching DB name). Do not treat it as authoritative app identity or domain guidance.
