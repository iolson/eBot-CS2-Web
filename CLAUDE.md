# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

eBot-CS2-Web is the web administration panel for the eBot CS2 game server bot. It manages competitive Counter-Strike 2 matches — creating matches, controlling match lifecycle, viewing live match data via WebSocket, and displaying player/match statistics. It is the web companion to the [eBot-CSGO](https://github.com/deStrO/eBot-CSGO) Node.js bot.

## Framework & Stack

- **Laravel 12** with PHP 8.4+, MySQL 8.4 LTS
- **Livewire 4** for reactive server-rendered UI
- **Tailwind CSS 4** via Vite build pipeline
- **Laravel Sanctum** for session + token auth
- **Pest PHP** for testing (on PHPUnit 12)
- **License**: Creative Commons BY 3.0

Legacy Symfony 1.4 code is preserved in `legacy/` for reference during migration.

## Common Commands

```bash
# Development
php artisan serve                        # Start dev server
npm run dev                              # Start Vite dev server (hot reload)
composer dev                             # Start all dev services (server, queue, logs, vite)

# Testing
./vendor/bin/pest                        # Run all tests
./vendor/bin/pest --filter=MatchTest     # Run specific test
./vendor/bin/pest tests/Feature/         # Run feature tests only
./vendor/bin/pest tests/Unit/            # Run unit tests only
./vendor/bin/pest --coverage             # Run with coverage report

# Code Quality
./vendor/bin/pint                        # Fix code style (PSR-12)
./vendor/bin/pint --test                 # Check code style without fixing

# Database
php artisan migrate                      # Run migrations
php artisan migrate:fresh --seed         # Reset DB and seed
php artisan db:seed                      # Run seeders

# Cache
php artisan optimize:clear               # Clear all caches
php artisan config:cache                 # Cache config for production

# Docker
docker compose up -d                     # Start all services
docker compose --profile dev up -d       # Start with Vite dev server
docker compose down                      # Stop all services
```

## Architecture

### Key Directories

- `app/` — Laravel application (Models, Services, Http Controllers, Livewire components)
- `config/ebot.php` — All eBot-specific configuration (connection, maps, defaults, Toornament)
- `resources/views/` — Blade templates and Livewire views
- `resources/views/components/layouts/` — Layout components (app, admin, widget, stream)
- `routes/web.php` — Web routes (public + admin)
- `routes/api.php` — API routes (JSON exports, Toornament webhooks)
- `legacy/` — Original Symfony 1.4 code preserved for reference

### Configuration

All configuration via `.env` file. Copy `.env.example` to `.env` for setup.

Key eBot settings: `EBOT_IP`, `EBOT_PORT`, `EBOT_WEBSOCKET_URL`, `EBOT_WEBSOCKET_SECRET_KEY`, `EBOT_DEMO_PATH`, `EBOT_MODE`.

Database must be `ebotv3` (shared with eBot Node.js server which writes directly to it).

### Critical Compatibility Constraints

1. **Shared database** — eBot Node.js writes match data directly to MySQL. Schema changes must be additive only.
2. **Socket.IO protocol** — Events (`matchsHandler`, `livemapHandler`, `rconSend`, `matchCommandSend`) must match exactly.
3. **AES-CTR encryption** — Match commands encrypted with `config_authkey`, must be decodable by eBot Node.js.
4. **JWT tokens** — HS256, 31-day TTL, `{admin, user, exp}` payload, signed with `websocket_secret_key`.

### Core Data Model

- **Matchs** (note: intentional non-standard plural) — Central entity. Status 0→14 (NOT_STARTED through ARCHIVE). Class is named `Matchs` (not `Match`) because `match` is a reserved keyword in PHP 8+. Use `use App\Models\Matchs;` directly — no aliasing needed.
- **Maps** — Individual map within a BO1/BO3/BO5 match.
- **Players** / **PlayersSnapshot** — Per-map stats and per-round snapshots.
- **RoundSummary** / **Round** / **PlayerKill** — Round-level event data.
- **Servers** — CS2 game servers (IP, RCON password, TV IP).
- **Teams** / **Seasons** — Organizational entities.

### Match Status Flow

```
NOT_STARTED(0) → STARTING(1) → WU_KNIFE(2) → KNIFE(3) → END_KNIFE(4)
→ WU_1_SIDE(5) → FIRST_SIDE(6) → WU_2_SIDE(7) → SECOND_SIDE(8)
→ WU_OT_1_SIDE(9) → OT_FIRST_SIDE(10) → WU_OT_2_SIDE(11) → OT_SECOND_SIDE(12)
→ END_MATCH(13) → ARCHIVE(14)
```

### Testing Requirements

All new features and modifications must include comprehensive tests. The test suite should cover:
- Unit tests for services and models
- Feature tests for all HTTP endpoints and Livewire components
- Compatibility tests verifying JWT/AES output matches eBot Node.js expectations
- Test coverage target: 80%+

### Third-Party Integrations

- **Toornament API** — Import/export matches to tournament platform (OAuth2 client credentials)
- **Socket.IO** — Real-time connection to eBot Node.js for match updates, RCON, live map
- **JWT** — WebSocket authentication tokens for eBot Node.js
