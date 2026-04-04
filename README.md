# EcolaCup
Laravel 11 project for the new EcolaCup (koneakce) website.
## Stack
- PHP 8.4 + Laravel 11
- PostgreSQL 16
- Vite + Tailwind + Alpine (Breeze Blade stack)
- DomPDF, Laravel Excel, Intervention Image
## Quick start (Docker)
1. Build and start:
   - `docker compose up -d --build`
2. Open website:
   - `http://localhost:8082`
3. Stop:
   - `docker compose down`
## Development mode (with source mount)
Use compose override for live code editing:
- `docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build`
## Services
- App container: `ecolacup-app` (Apache + PHP), exposed on port `8082`
- Database container: `ecolacup-postgres` (PostgreSQL 16), exposed on port `5432`
## Database
Database defaults are configured for PostgreSQL:
- DB: `ecolacup`
- User: `ecolacup`
- Password: `ecolacup`
Migrations run automatically at container startup in `docker/entrypoint.sh`.
## Initial implementation scope
This first version includes:
- Laravel + Breeze scaffold
- Core package dependencies from the plan
- Initial domain schema migrations (`osoby`, `kone`, `udalosti`, `prihlasky`, `clenstvi_cmt`, etc.)
- Dockerized deployment baseline for quick server spawn
