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
   - `http://localhost:8086`
3. Stop:
   - `docker compose down`
4. Public tunnel:
   - not included in the default local stack
   - if you ever need one, copy `docker-compose.cloudflare.yml.example` to a private override and provide your own `CLOUDFLARE_TUNNEL_TOKEN`
## Development mode (with source mount)
Use compose override for live code editing:
- `docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build`
## Services
- App container: `ecolacup-app` (Apache + PHP), exposed on port `8086`
- Database container: `ecolacup-postgres` (PostgreSQL 16), exposed on port `5432`
- No Cloudflare tunnel is started by default in local development
## Database
Database defaults are configured for PostgreSQL:
- DB: `ecolacup`
- User: `ecolacup`
- Password: `ecolacup`
Migrations run automatically at container startup in `docker/entrypoint.sh`.
## Queue & Scheduler (B8 readiness)
- Queue tables are included in migrations (`jobs`, `job_batches`, `failed_jobs`).
- Production worker template is stored in `deploy/supervisor/ecolacup-worker.conf`.
- Registered scheduled maintenance tasks live in `routes/console.php`:
  - `auth:clear-resets` every 15 minutes
  - `queue:prune-batches --hours=48` daily at `02:15`
  - `queue:prune-failed --hours=168` daily at `02:30`
- Example scheduler cron:
  - `* * * * * cd /var/www/ecolacup && php artisan schedule:run >> /dev/null 2>&1`
## Deployment notes
- The Docker app container runs `docker/entrypoint.sh` on start.
- Startup tasks include:
  - creating `storage`/`bootstrap` cache directories
  - generating `APP_KEY` when missing
  - running `php artisan storage:link`
  - running `php artisan migrate --force --graceful`
- The current production-like container stack is PostgreSQL-based (`DB_CONNECTION=pgsql`), not MySQL-based.
## Initial implementation scope
This first version includes:
- Laravel + Breeze scaffold
- Core package dependencies from the plan
- Initial domain schema migrations (`osoby`, `kone`, `udalosti`, `prihlasky`, etc.)
- Dockerized deployment baseline for quick server spawn
