# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

eBot-CS2-Web is the web administration panel for the eBot CS2 game server bot. It manages competitive Counter-Strike 2 matches — creating matches, controlling match lifecycle, viewing live match data via WebSocket, and displaying player/match statistics. It is the web companion to the [eBot-CSGO](https://github.com/deStrO/eBot-CSGO) Node.js bot.

## Framework & Stack

- **Symfony 1.4** (bundled in `lib/vendor/symfony/`) with **Doctrine 1.x ORM**
- **No modern dependency management** — no Composer, no npm. All dependencies are vendored.
- **PHP 5.3+** with MySQL and Sockets extensions required
- **Frontend**: Bootstrap 2.x, jQuery 1.8.2, Highcharts, Socket.IO, heatmap.js — no build tools
- **Auth**: sfDoctrineGuardPlugin (sfGuard) with SHA1 passwords
- **License**: Creative Commons BY 3.0

## Common Commands

```bash
# Symfony CLI (all tasks run through this)
php symfony cache:clear                  # Clear app cache (do this after config/code changes)
php symfony doctrine:build-model         # Regenerate model classes from schema.yml
php symfony doctrine:build-schema        # Generate schema from existing DB
php symfony doctrine:insert-sql          # Create/update tables from schema
php symfony doctrine:data-load           # Load fixtures from data/fixtures/

# Tests (Symfony's lime framework)
php symfony test:functional backend      # Run all backend functional tests
php symfony test:functional frontend     # Run all frontend functional tests
php symfony test:unit                    # Run unit tests
php test/functional/backend/matchsActionsTest.php  # Run a single test
```

## Architecture

### Two Separate Apps, Two Entry Points

| Entry Point | App | Auth | Purpose |
|---|---|---|---|
| `web/index.php` | frontend | Public | Match viewing, stats, livemap |
| `web/admin.php` | backend | Requires `admin` credential | Full CRUD: matches, servers, teams, seasons |

### Key Directories

- `apps/backend/modules/` — Admin modules: matchs, servers, teams, seasons, configs, advertising, users, stats
- `apps/frontend/modules/` — Public modules: matchs, stats, stream, widget, seasons
- `config/doctrine/schema.yml` — Full ORM schema definition (source of truth for data models)
- `config/app.yml` + `config/app_user.yml` — App settings (ebot connection, websocket URL, maps list, defaults)
- `lib/model/doctrine/` — Doctrine model classes (Matchs, Maps, Players, Servers, Teams, etc.)
- `web/` — Document root for Apache (static assets: css/, js/, images/)
- `web/installation/` — Web-based installation wizard (steps 0-6)

### Configuration

Copy defaults before first run:
- `config/databases.yml.default` → `config/databases.yml` (MySQL credentials, database `ebotv3`)
- `config/app_user.yml.default` → `config/app_user.yml` (ebot IP/port, websocket URL, JWT secret, demo paths)

### Core Data Model

- **Matchs** — Central entity. Status progresses 0→14 (NOT_STARTED through ARCHIVE). Links to teams, server, season, maps.
- **Maps** — Individual map within a BO1/BO3/BO5 match. Tracks scores, sides, overtime.
- **Players** / **PlayersSnapshot** — Per-map player stats and per-round snapshots.
- **RoundSummary** / **Round** / **PlayerKill** — Round-level event data.
- **Servers** — CS2 game servers (IP, RCON password, TV IP).
- **Teams** / **Seasons** — Organizational entities.

### Real-Time Communication

The web panel connects to the eBot Node.js server via Socket.IO. JWT tokens (signed with `websocket_secret_key` from config) authenticate the WebSocket connection. The live map feature shows real-time player positions during matches.

### Internationalization

Three languages supported: English (en), Russian (ru), Chinese (cn). Translation files at `apps/*/i18n/*/messages.xml`. Language switch via POST to `/switch/lang/:langage`.

### Match Status Flow

```
NOT_STARTED(0) → STARTING(1) → WU_KNIFE(2) → KNIFE(3) → END_KNIFE(4)
→ WU_1_SIDE(5) → FIRST_SIDE(6) → WU_2_SIDE(7) → SECOND_SIDE(8)
→ WU_OT_1_SIDE(9) → OT_FIRST_SIDE(10) → WU_OT_2_SIDE(11) → OT_SECOND_SIDE(12)
→ END_MATCH(13) → ARCHIVE(14)
```

### Third-Party Integrations

- **Toornament API** (`lib/ToornamentAPI.class.php`) — Import/export matches to tournament platform
- **JWT** (`lib/JWT.class.php`) — WebSocket authentication tokens
