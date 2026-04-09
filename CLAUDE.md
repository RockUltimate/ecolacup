# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this project is

Czech equestrian event management system (Ecolakone.cz). It manages:
- User accounts with GDPR consent tracking
- Rider profiles (`osoby`) and horse records (`koně`)
- Event creation with disciplines (`moznosti`) and stabling options (`ustajeni`)
- Event registrations (`prihlasky`) with pricing, PDF generation, and email dispatch
- Admin reporting with Excel/PDF exports

## Tech stack

PHP 8.4, Laravel 11, PostgreSQL 16, Vite + Tailwind CSS + Alpine.js, DomPDF, Laravel Excel.

## Commands

### Local development (no Docker)
```bash
composer dev        # starts PHP server + queue listener + log viewer + Vite concurrently
php artisan test    # run full test suite
php artisan test --filter TestName   # run a single test
npm run build       # production frontend build
```

### Docker (local-only)
```bash
docker compose up -d                        # start local services (app, postgres, queue)
docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d   # dev mode with live volume mount
docker compose exec app php artisan migrate --force   # run migrations inside container
docker compose build app && docker compose up -d app  # rebuild after adding migrations/code changes
```

Cloudflare tunnel is intentionally excluded from the default repo compose file. If a tunnel is needed, define it in a private override outside the committed local stack.

**Important:** The app image bakes in all code at build time. After adding new migration files, you must rebuild the image (`docker compose build app`) — copying the file with `docker cp` works as a quick fix but won't survive a container restart.

The `docker/entrypoint.sh` runs automatically on container start: creates storage dirs, generates `APP_KEY` if missing, runs `php artisan migrate --force --graceful`.

### Tests use SQLite in-memory
`phpunit.xml` overrides `DB_CONNECTION=sqlite` with `:memory:`. No PostgreSQL needed to run tests.

## Architecture

### Key route groups
- **Public** (`/`, `/udalosti`) — event listing and detail, no auth
- **Authenticated** (`/ucet`, `/osoby`, `/kone`, `/prihlasky`) — user-facing CRUD
- **Admin** (`/admin`, requires `auth` + `admin` middleware) — event management, user management, reporting/exports, start numbers

### Controllers
- `App\Http\Controllers\OsobaController` / `KunController` — standard CRUD for riders and horses
- `App\Http\Controllers\PrihlaskaController` — complex: orchestrates discipline selection, stabling, tandem horse, pricing calculation, PDF generation, email dispatch
- `App\Http\Controllers\Admin\UdalostController` — event CRUD + nested discipline and stabling option management
- `App\Http\Controllers\Admin\ReportController` — 12+ report/export endpoints (Excel + PDF)

### Database model relationships
```
User → hasMany Osoba, Kun, Prihlaska
Udalost → hasMany UdalostMoznost (disciplines), UdalostUstajeni (stabling options), Prihlaska
Prihlaska → belongsTo Udalost, User, Osoba, Kun
         → hasMany PrihlaskaPolozka (line items), PrihlaskaUstajeni (stabling choices)
PrihlaskaPolozka → belongsTo UdalostMoznost (moznost_id)
PrihlaskaUstajeni → belongsTo UdalostUstajeni (ustajeni_id)
```

`Osoba`, `Kun`, `Udalost`, `Prihlaska` all use `SoftDeletes`.

### Foreign key cascade pattern
When adding a new FK from a registration/child table back to an event option table, always add `->cascadeOnDelete()`. Missing cascade on `prihlasky_ustajeni.ustajeni_id` and `prihlasky_polozky.moznost_id` previously caused 500 errors on delete — both fixed in migrations `2026_04_07_000000` and `2026_04_07_000001`.

### Czech locale specifics
- `App\Support\CzechDate` utility parses Czech-formatted dates (used in `OsobaController`)
- All user-facing text is Czech; blade views use Czech variable/route names (`udalosti`, `prihlasky`, `moznosti`, `ustajeni`, `kone`, `osoby`)
- `APP_LOCALE=cs`, `APP_TIMEZONE=Europe/Prague`

### CI/CD
GitHub Actions (`.github/workflows/laravel.yml`) runs on push/PR to `main`: installs deps, builds frontend, runs `php artisan test`. Uses PHP 8.4 + Node 22 with SQLite for tests.
