# eBot CS2 Web

Web administration panel for the [eBot-CSGO](https://github.com/deStrO/eBot-CSGO) Node.js game server bot. Manages competitive Counter-Strike 2 matches — creating matches, controlling match lifecycle, viewing live match data, and displaying player/match statistics.

This is a **Laravel 12** modernization of the original Symfony 1.4 panel. The legacy code is preserved in `legacy/` for reference.

## Requirements

- PHP 8.4+
- Node.js 22+
- MySQL 8.4 LTS (database name must be `ebotv3` — shared with eBot Node.js)
- Composer 2

## Quick Start

### Option A — Local PHP + Docker DB (recommended for development)

```bash
# 1. Start only the database services
docker compose up -d mysql redis

# 2. Install dependencies
composer install
npm install

# 3. Configure environment
cp .env.example .env
php artisan key:generate
```

Edit `.env` with the Docker database credentials:
```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ebotv3
DB_USERNAME=ebot
DB_PASSWORD=ebot
```

```bash
# 4. Run migrations
php artisan migrate

# 5. (Optional) Seed sample data for local testing
php artisan db:seed

# 6. Run the install wizard to create your admin account
php artisan ebot:install

# 7. Start dev servers (PHP + Vite together)
composer dev
```

App is at **http://localhost:8000**

### Option B — Full Docker

```bash
composer install          # vendor/ must exist before mounting
cp .env.example .env
# Edit .env: DB_HOST=mysql, REDIS_HOST=redis

docker compose --profile dev up -d
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan ebot:install
```

App is at **http://localhost:8080**

## Common Commands

```bash
composer dev                             # Start PHP server + queue + logs + Vite
php artisan migrate                      # Run pending migrations
php artisan migrate:fresh --seed         # Wipe and re-seed (local only)
php artisan ebot:install                 # First-time setup wizard
php artisan ebot:reset-password          # Reset an admin user's password (CLI)
./vendor/bin/pest --no-coverage          # Run test suite
./vendor/bin/pint                        # Fix code style (PSR-12)
```

## Configuration

All settings are in `.env`. Key eBot-specific variables:

| Variable | Description | Default |
|---|---|---|
| `EBOT_WEBSOCKET_URL` | eBot Node.js Socket.IO URL | `http://localhost:12360` |
| `EBOT_WEBSOCKET_SECRET_KEY` | JWT signing secret (must match eBot config) | — |
| `EBOT_MODE` | `lan` shows server IPs, `net` hides them | `net` |
| `EBOT_DEMO_PATH` | Path to eBot demo directory | `../../ebot-csgo/demos` |
| `EBOT_DEMO_DOWNLOAD` | Enable demo download button on match pages | `true` |
| `EBOT_MAPS` | Comma-separated map pool shown during match creation | ESL Pro Tour 2026 pool (7 maps) |

## Local Development Gotchas

### MySQL port conflict
If you have a local MySQL instance (e.g. Homebrew), it binds `127.0.0.1:3306` before Docker can. Laravel will silently connect to your local MySQL instead of the Docker one, which won't have the `ebot` user.

**Fix:** Stop local MySQL before starting Docker services.
```bash
brew services stop mysql
docker compose up -d mysql redis
```
Or change Docker to map a different port (`3307:3306`) and set `DB_PORT=3307` in `.env`.

### Stale config cache after editing .env
If you see database errors after changing `.env`, clear the config cache:
```bash
php artisan config:clear
```

### Updating the map pool
When Valve rotates the active duty map pool, update `EBOT_MAPS` in `.env`:
```
EBOT_MAPS=de_dust2,de_inferno,de_mirage,de_nuke,de_overpass,de_ancient,de_anubis
```
Then clear the config cache:
```bash
php artisan config:clear
```
No code change or server redeploy required. The new pool appears immediately in the match creation form.

### APP_INSTALLED flag
All routes redirect to `/install` until `APP_INSTALLED=true` is set in `.env`. The install wizard sets this automatically. To skip the wizard in development, set it manually:
```bash
# .env
APP_INSTALLED=true
```

## Testing

```bash
./vendor/bin/pest --no-coverage          # Run all 268 tests
./vendor/bin/pest tests/Feature/         # Feature tests only
./vendor/bin/pest tests/Unit/            # Unit tests only
XDEBUG_MODE=coverage ./vendor/bin/pest --coverage-text  # With coverage (requires Xdebug)
```

Tests use an in-memory SQLite database — no MySQL required for the test suite.

## License

[Creative Commons BY 3.0](http://creativecommons.org/licenses/by/3.0/)

Original authors: Julien 'deStrO' Pardons, Fabian 'Basert' Gruber
