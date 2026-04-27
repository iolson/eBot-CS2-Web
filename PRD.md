# Product Requirements Document: eBot-CS2-Web Modernization

## 1. Executive Summary

Modernize the eBot-CS2-Web application from Symfony 1.4 (EOL since 2012) to Laravel 12, replacing all deprecated dependencies while maintaining **full backward compatibility** with the eBot-CS2 Node.js bot and the shared MySQL database it writes to directly.

## 2. Current State

| Component | Current | Issue |
|---|---|---|
| Framework | Symfony 1.4 | EOL since Nov 2012, no security patches |
| PHP | 5.3+ target | PHP 5.x EOL; app likely runs on 7.x/8.x with shims |
| ORM | Doctrine 1.x | Bundled, no Composer, no updates |
| Auth | sfDoctrineGuardPlugin | SHA1 passwords, no 2FA, no modern session handling |
| Frontend | Bootstrap 2.x, jQuery 1.8 | Major version behind, no build pipeline |
| Dependencies | All vendored in `lib/vendor/` | No package manager, no update path |
| Tests | Symfony lime framework | Minimal coverage, framework-coupled |
| Real-time | Raw Socket.IO client JS | Works but tightly coupled to inline PHP templates |

## 3. Target Technology Stack

| Component | Version | Rationale |
|---|---|---|
| **Laravel** | **12.x** | Current stable (Feb 2025). Bug fixes until Aug 2026, security until Feb 2027. Battle-tested. |
| **PHP** | **8.4.x** | Active support until Dec 2028. Required minimum for Laravel 12 is 8.2. |
| **MySQL** | **8.4 LTS** | Only active LTS track. MySQL 8.0 reached EOL April 2026. |
| **Eloquent ORM** | (ships with Laravel 12) | Replaces Doctrine 1.x |
| **Laravel Sanctum** | (ships with Laravel 12) | Session + token auth. Replaces sfGuard. |
| **Laravel Reverb** | Latest stable | First-party WebSocket server for real-time features |
| **Laravel Echo** | Latest stable | JS WebSocket client, replaces raw Socket.IO |
| **Tailwind CSS 4** | Latest stable | Ships with Laravel 12 starter kits. Replaces Bootstrap 2. |
| **Livewire 4** | Latest stable | PHP-first reactive UI. Closest migration path from server-rendered Symfony templates. |
| **Vite** | (ships with Laravel 12) | Default build tool. Replaces zero-build-tool static assets. |
| **PHPUnit 11** | (ships with Laravel 12) | Replaces Symfony lime tests. |
| **Pest PHP** | Latest stable | Optional layer on PHPUnit for cleaner test syntax. |

### Frontend Architecture Decision: Livewire 4

**Why Livewire over Inertia.js (React/Vue/Svelte):**

1. **Migration path** — Current app is server-rendered PHP templates. Livewire is server-rendered PHP with reactive sprinkles. Moving from Symfony templates to Blade+Livewire is the most direct translation.
2. **Team expertise** — eBot community is PHP-centric. Livewire keeps the entire stack in PHP.
3. **Real-time** — Livewire 4 has native polling and event listeners, which map well to the existing match status refresh patterns.
4. **Complexity** — No separate JS build for the frontend framework. Vite only handles Tailwind CSS and Alpine.js (bundled with Livewire).

For the live map canvas rendering and heatmaps, we will use **Alpine.js** (bundled with Livewire) + vanilla JS, since these are inherently client-side Canvas/WebSocket operations.

## 4. Critical Compatibility Constraints

These are non-negotiable requirements that every phase must respect:

### 4.1 Shared Database (eBot Node.js writes directly)

The eBot Node.js bot reads match configuration and writes ALL match state (scores, players, rounds, kills, heatmaps) directly to MySQL. The web panel is primarily a **reader** for match data and a **writer** only for configuration (match creation, server/team/season CRUD).

**Constraint:** The database schema must remain backward-compatible. Table names, column names, column types, and enum values must not change. New columns may be added but existing ones must not be renamed or removed.

**Migration strategy:** Laravel migrations will describe the existing schema as-is (not modify it). Any new Laravel-specific tables (e.g., `sessions`, `cache`, `jobs`, `password_reset_tokens`) will be added alongside the existing tables.

### 4.2 Socket.IO Protocol

The eBot Node.js server uses Socket.IO (not native WebSockets). The web panel must continue to connect to the eBot Socket.IO server at the configured `websocket_url`.

**Events that must be preserved exactly:**

| Direction | Event | Purpose |
|---|---|---|
| Client → Server | `identify` | `{type: "matchs"\|"livemap"\|"rcon"\|"logger", match_id: <id>}` |
| Client → Server | `matchCommandSend` | `JSON.stringify([AES_encrypted_command, server_ip])` |
| Client → Server | `rconSend` | `JSON.stringify([AES_encrypted_command, server_ip])` |
| Server → Client | `matchsHandler` | Match status/score/team updates |
| Server → Client | `livemapHandler` | Kill positions and round changes |
| Server → Client | `rconHandler` | `{content: "<rcon_response>"}` |
| Server → Client | `loggerHandler` | Server log lines |

**AES-CTR 256-bit encryption** of commands using `match.config_authkey` must be preserved. The JS-side `Aes.Ctr.encrypt()` implementation must remain compatible with eBot Node.js decryption.

**Note:** Laravel Reverb will be used for any **new** real-time features (e.g., push notifications, admin alerts). The existing Socket.IO connection to the eBot Node.js server is **separate** and must remain as a direct client connection.

### 4.3 JWT Authentication for WebSocket

JWT tokens signed with `websocket_secret_key` (HS256, 31-day TTL) must continue to be issued. The payload format `{admin: bool, user: string, exp: timestamp}` must match what the eBot Node.js server expects.

### 4.4 Demo File Serving

Demo files are served from the eBot Node.js server's filesystem (`app_demo_path`). The new app must support both `X-Sendfile` (Apache) and `X-Accel-Redirect` (Nginx) for efficient file serving, plus PHP `readfile()` fallback.

### 4.5 Toornament API

The Toornament integration (OAuth2 client credentials, match import/export, round stats push) must be preserved. The `identifier_id` format (`<tournamentId>.<matchId>.<gameNumber>`) is shared with external systems.

### 4.6 Match Control Commands

The predefined command vocabulary must be preserved exactly: `stopNoRs`, `stop`, `forcestart`, `forceknife`, `passknife`, `forceknifeend`, `stopback`, `pauseunpause`, `streamerready`, `skipmapprev`, `skipmapnext`.

### 4.7 config_authkey Generation

When a match is started, `config_authkey` is generated as `uniqid(mt_rand(), true)`. The eBot Node.js server uses this same key to decrypt commands. The generation method must produce keys compatible with the existing AES-CTR implementation.

## 5. Migration Phases

Each phase is a separate branch, merged sequentially. Each branch must result in a **working application** (no broken intermediate states on the main branch).

---

### Phase 1: Laravel Project Scaffolding

**Branch:** `modernize/01-laravel-scaffold`

**Goal:** Initialize a fresh Laravel 12 project structure alongside the existing Symfony code, establishing the foundation.

**Tasks:**
1. Install Laravel 12 via Composer into the project root
2. Configure PHP 8.4 as minimum in `composer.json`
3. Install Livewire 4 starter kit (`php artisan install:livewire`)
4. Configure `.env` / `.env.example` with all settings from current `config/app_user.yml.default` and `config/databases.yml.default`:
   - `DB_DATABASE=ebotv3`
   - `EBOT_IP`, `EBOT_PORT`, `EBOT_WEBSOCKET_URL`
   - `EBOT_WEBSOCKET_SECRET_KEY`
   - `EBOT_LOG_MATCH_PATH`, `EBOT_LOG_MATCH_ADMIN_PATH`, `EBOT_DEMO_PATH`
   - `EBOT_MODE` (lan/net)
   - `EBOT_DEFAULT_MAX_ROUND`, `EBOT_DEFAULT_RULES`, `EBOT_DEFAULT_OVERTIME_*`
   - `EBOT_DEMO_DOWNLOAD`
   - `TOORNAMENT_ID`, `TOORNAMENT_SECRET`, `TOORNAMENT_API_KEY`, `TOORNAMENT_PLUGIN_KEY`
5. Configure Vite with Tailwind CSS 4
6. Set up basic Blade layout templates (admin + public) replacing `layout.php`
7. Add `config/ebot.php` configuration file mapping `.env` values
8. Set up Docker Compose for local development (PHP 8.4-FPM, MySQL 8.4, Nginx, Redis)
9. Add `.editorconfig`, updated `.gitignore` for Laravel

**Deliverable:** `php artisan serve` runs, shows Laravel welcome page. Symfony code still present but unused.

---

### Phase 2: Database — Eloquent Models & Migrations

**Branch:** `modernize/02-database-models`

**Goal:** Create Laravel migrations that describe the existing schema exactly, and Eloquent models for all tables.

**Tasks:**
1. Create migrations for all existing tables **matching the current schema exactly**:
   - `matchs`, `maps`, `maps_score`, `players`, `players_snapshot`
   - `round`, `round_summary`, `player_kill`, `players_heatmap`
   - `servers`, `seasons`, `teams`, `teams_in_seasons`
   - `advertising`, `configs`
   - `sf_guard_user`, `sf_guard_group`, `sf_guard_permission` and join tables
   - `sf_guard_remember_key`, `sf_guard_forgot_password`
2. Create migrations for new Laravel tables: `sessions`, `cache`, `jobs`, `failed_jobs`, `password_reset_tokens`
3. Create Eloquent models with:
   - Correct table names (e.g., `protected $table = 'matchs'` — note the intentional non-standard plural)
   - All relationships (belongsTo, hasMany, belongsToMany)
   - Status constants on the `Match` model (0–14)
   - Attribute casting (booleans, integers, enums where appropriate)
   - Scopes for common queries (live matches, archived, by season)
4. Create model factories and seeders for development data
5. Verify migrations can run against an empty database AND that the existing populated database works with the new models

**Key models and relationships:**
```
Match → hasMany Maps, hasMany Players, hasMany Rounds, hasMany RoundSummaries, hasMany PlayerKills
Match → belongsTo Server, belongsTo Season, belongsTo Team (×2: team_a, team_b)
Map → belongsTo Match, hasMany Players, hasMany MapScores, hasMany PlayerKills
Player → belongsTo Match, belongsTo Map, hasMany PlayerSnapshots
Season → hasMany Matches, belongsToMany Teams (via teams_in_seasons), hasMany Advertisings
Server → hasMany Matches
Team → hasMany Matches, belongsToMany Seasons
```

**Deliverable:** `php artisan migrate` creates identical schema. `php artisan tinker` can query existing data via Eloquent.

---

### Phase 3: Authentication & Authorization

**Branch:** `modernize/03-authentication`

**Goal:** Replace sfDoctrineGuardPlugin with Laravel Sanctum + Fortify, while supporting existing SHA1 password hashes during transition.

**Tasks:**
1. Configure Laravel Sanctum for session-based auth (admin panel) and API tokens (future use)
2. Implement custom `UserProvider` that:
   - Reads from `sf_guard_user` table initially
   - Validates SHA1 passwords (matching sfGuard's `sha1($salt . $password)` scheme)
   - On successful login, **rehashes to bcrypt** and stores in a new `password` column (transparent upgrade)
3. Create `User` Eloquent model mapped to `sf_guard_user` with:
   - `is_admin` accessor (checks `sf_guard_user_permission` or `sf_guard_user_group` for admin credential)
   - Laravel's `Authenticatable` contract
4. Implement middleware:
   - `admin` middleware — checks admin credential (replaces Symfony's `security.yml` credential check)
   - Applied to all `/admin/*` routes
5. Create auth views with Livewire: login, logout (registration optional — controlled by config like current `app_register_active`)
6. Implement "remember me" functionality (replaces `sf_guard_remember_key`)
7. Create migration to add `password` (bcrypt) column to `sf_guard_user` alongside existing `password` (SHA1) column, or add a new `users` table that references `sf_guard_user.id`

**Deliverable:** Admin can log in with existing credentials. Password silently upgraded to bcrypt on login. Unauthorized users redirected to login page for admin routes.

---

### Phase 4: Configuration & Service Layer

**Branch:** `modernize/04-services-config`

**Goal:** Create the service layer and configuration infrastructure that controllers will depend on.

**Tasks:**
1. Create `config/ebot.php` with all eBot settings (maps list, defaults, paths, connection info)
2. Create service classes:
   - `App\Services\EbotConnectionService` — manages connection info, JWT token generation for WebSocket auth
   - `App\Services\MatchService` — match creation, configuration, status helpers, command dispatch
   - `App\Services\DemoService` — demo file location, download (X-Sendfile/X-Accel-Redirect/readfile)
   - `App\Services\ToornamentService` — replaces `ToornamentAPI.class.php` (OAuth2, import/export, round stats)
   - `App\Services\EncryptionService` — AES-CTR 256-bit encryption for Socket.IO commands (preserves compatibility with eBot Node.js decryption)
   - `App\Services\JwtService` — JWT token generation (HS256, 31-day TTL, `{admin, user, exp}` payload) for WebSocket auth
   - `App\Services\HeatmapService` — heatmap data aggregation and map coordinate transformations
3. Create Laravel Form Requests for validation:
   - `StoreMatchRequest`, `UpdateMatchRequest`
   - `StoreServerRequest`, `StoreTeamRequest`, `StoreSeasonRequest`
   - `StoreAdvertisingRequest`, `StoreConfigRequest`
4. Register service providers and facades as needed
5. Port `ScoreColorUtils` as a Blade directive or helper

**Deliverable:** All services unit-tested. JWT tokens validate against existing eBot Node.js server. AES encryption produces output decodable by eBot.

---

### Phase 5: Admin Panel — Routes & Controllers

**Branch:** `modernize/05-admin-controllers`

**Goal:** Implement all backend (admin) routes and controllers.

**Tasks:**
1. Define admin routes in `routes/web.php` (grouped under `/admin` prefix with `admin` middleware):

   **Matches:**
   - `GET /admin/matchs` — list (in-progress + finished)
   - `GET /admin/matchs/create` — create form
   - `POST /admin/matchs` — store
   - `GET /admin/matchs/{match}` — view/detail
   - `GET /admin/matchs/{match}/edit` — edit form
   - `PUT /admin/matchs/{match}` — update
   - `DELETE /admin/matchs/{match}` — delete
   - `POST /admin/matchs/{match}/start` — start (set enable=1)
   - `POST /admin/matchs/{match}/stop` — stop
   - `POST /admin/matchs/{match}/reset` — reset
   - `POST /admin/matchs/{match}/archive` — archive (status→14)
   - `POST /admin/matchs/{match}/cancel-archive` — un-archive
   - `POST /admin/matchs/{match}/duplicate` — clone match
   - `POST /admin/matchs/{match}/forcestart` — force start (emit Socket.IO)
   - `POST /admin/matchs/{match}/pause` — pause/unpause
   - `POST /admin/matchs/{match}/knife/*` — knife round controls
   - `POST /admin/matchs/{match}/streamer-ready`
   - `GET /admin/matchs/{match}/rcon` — RCON terminal view
   - `GET /admin/matchs/{match}/logs` — server log view
   - `GET /admin/matchs/{match}/heatmap` — heatmap data (JSON)
   - `POST /admin/matchs/{match}/actions` — dynamic action buttons (JSON)
   - Match stats sub-views: kills, rounds, players, weapons

   **CRUD Resources:**
   - `resource /admin/servers`
   - `resource /admin/teams`
   - `resource /admin/seasons`
   - `resource /admin/advertising`
   - `resource /admin/configs`
   - `resource /admin/users`

   **Toornament:**
   - `GET /admin/toornament/tournaments` — list tournaments
   - `POST /admin/matchs/toornament/import/{tournamentId}/{matchId}/{gameId}` — import match

2. Implement controllers following Laravel resource conventions
3. Use Form Requests for validation (from Phase 4)
4. Use policies for authorization where appropriate

**Deliverable:** All admin routes functional. CRUD operations work against the existing database.

---

### Phase 6: Public Frontend — Routes & Controllers

**Branch:** `modernize/06-frontend-controllers`

**Goal:** Implement all public-facing routes and controllers.

**Tasks:**
1. Define public routes in `routes/web.php`:
   - `GET /` — homepage (current matches, live scores)
   - `GET /matchs/current` — live matches list
   - `GET /matchs/archived` — archived matches (paginated)
   - `GET /matchs/{match}` — match detail (scoreboard, rounds, player stats)
   - `GET /matchs/{match}/livemap` — embeddable live map
   - `GET /matchs/{match}/coverage` — broadcast overlay widget
   - `GET /matchs/demo/{map}` — demo file download
   - `GET /stats/players/{player}` — player career stats
   - `GET /stats/maps` — map statistics
   - `GET /seasons/{season}` — season view
   - `GET /seasons/{season}/ranking` — season team rankings
   - `GET /widget/match/{match}/players/stat` — embeddable player stats widget

2. Define API routes in `routes/api.php`:
   - `GET /api/matchs/{match}/players` — player stats JSON
   - `GET /api/matchs/{match}/rounds` — round summary JSON
   - `GET /api/matchs/{match}/kills` — kill data JSON
   - `GET /api/matchs/{match}/estats` — full export JSON
   - `POST /api/matchs/{match}/toornament/export` — push to Toornament (with plugin key validation)

3. Implement `DemoController` with content negotiation for X-Sendfile/X-Accel-Redirect/readfile
4. Implement season switching (stored in session)
5. Implement language switching (en/ru/cn) via Laravel localization

**Deliverable:** All public pages functional. Demo downloads work. JSON exports match existing format.

---

### Phase 7: Blade Templates & Livewire Components

**Branch:** `modernize/07-views-livewire`

**Goal:** Convert all Symfony PHP templates to Blade + Livewire, with Tailwind CSS replacing Bootstrap 2.

**Tasks:**
1. Create Blade layouts:
   - `layouts/admin.blade.php` — admin panel shell (navigation, Socket.IO script injection, JWT token)
   - `layouts/public.blade.php` — public site shell
   - `layouts/widget.blade.php` — minimal layout for embeddable widgets
   - `layouts/stream.blade.php` — transparent layout for broadcast overlays

2. Create Livewire components for reactive UI:
   - `MatchList` — live-updating match list with status/score (replaces AJAX polling + Socket.IO `matchsHandler`)
   - `MatchDetail` — tabbed match view (scoreboard, rounds, players, weapons, heatmap)
   - `MatchActions` — admin action buttons that update based on match status
   - `MatchCreateForm` — multi-step match creation (team selection, server selection, map mode, config options)
   - `ServerList`, `TeamList`, `SeasonList` — CRUD tables with inline editing
   - `RconTerminal` — RCON command input/output (wraps Socket.IO `rconSend`/`rconHandler`)
   - `LogViewer` — streaming log display (wraps Socket.IO `loggerHandler`)
   - `PlayerStatsTable` — sortable player statistics

3. Create Alpine.js components for client-side-only features:
   - `livemap.js` — Canvas-based live map rendering (kill positions, round tracking via Socket.IO `livemapHandler`)
   - `heatmap.js` — heatmap.js integration with map overlay
   - `socket-manager.js` — Socket.IO connection management, event routing, AES encryption

4. Port all match status display logic (status labels, color coding, progress indicators)
5. Port the statistics charts (Highcharts → keep Highcharts or migrate to Chart.js/ApexCharts)
6. Implement DataTables-equivalent sorting/filtering with Livewire or Alpine
7. Port flag icons and country code display
8. Ensure all embeddable widgets (livemap, coverage, player stats) work in iframe contexts

**Deliverable:** All pages rendered with new Blade/Livewire templates. Visual parity with existing UI (modernized styling). All interactive features functional.

---

### Phase 8: Real-Time Integration

**Branch:** `modernize/08-realtime`

**Goal:** Implement the real-time WebSocket communication layer that connects to the eBot Node.js Socket.IO server.

**Tasks:**
1. Create a dedicated JS module (`resources/js/ebot-socket.js`) that:
   - Connects to the eBot Socket.IO server at `EBOT_WEBSOCKET_URL`
   - Passes JWT token in `auth.token` header
   - Exposes typed event handlers for `matchsHandler`, `livemapHandler`, `rconHandler`, `loggerHandler`
   - Sends `identify` messages on page/component mount
   - Sends encrypted `matchCommandSend` and `rconSend` commands

2. Bundle the AES-CTR encryption library as an ES module (port the existing `aes.js` / `aes-ctr.js`)

3. Integrate with Livewire components:
   - `MatchList` receives `matchsHandler` events → updates match cards in real-time
   - `MatchDetail` receives score/status updates
   - `RconTerminal` sends/receives RCON commands
   - `LogViewer` streams log lines

4. Integrate with Alpine.js components:
   - `livemap.js` receives `livemapHandler` kill events → renders on canvas
   - Coverage widget (broadcast overlay)

5. **Optional:** Install Laravel Reverb for future server-originated push events (admin notifications, system alerts). This is separate from the eBot Socket.IO connection.

6. Handle reconnection, connection state display ("Live Refresh: online/offline"), and error states

**Deliverable:** Real-time match updates work. RCON terminal functional. Live map shows kills. All existing Socket.IO features operational.

---

### Phase 9: Toornament & External Integrations

**Branch:** `modernize/09-integrations`

**Goal:** Port the Toornament API integration and any other external service integrations.

**Tasks:**
1. Implement `ToornamentService` using Laravel HTTP client:
   - OAuth2 client credentials flow with token caching (Laravel Cache instead of filesystem)
   - Tournament listing, match/game fetching
   - Result push (scores, maps, round-by-round stats)
   - `identifier_id` parsing and generation (`<tournamentId>.<matchId>.<gameNumber>`)
2. Implement `X-Plugin-Key` header validation middleware for external Toornament export endpoint
3. Create Toornament admin UI (tournament browser, import wizard)
4. Port match export endpoints (players, rounds, kills, estats) with identical JSON structure
5. Add rate limiting to API endpoints (`routes/api.php`)

**Deliverable:** Toornament import/export works. JSON export format identical to existing.

---

### Phase 10: Internationalization

**Branch:** `modernize/10-i18n`

**Goal:** Port the three-language support (en, ru, cn) to Laravel's localization system.

**Tasks:**
1. Convert Symfony `messages.xml` files to Laravel `lang/{locale}/*.php` files:
   - `lang/en/`, `lang/ru/`, `lang/cn/`
2. Map all `__()` translation keys
3. Implement language switcher (session-based, matching current `POST /switch/lang/:langage` behavior)
4. Set default locale from config
5. Ensure all Blade templates use `{{ __('key') }}` or `@lang('key')`

**Deliverable:** All three languages work. Language persists in session.

---

### Phase 11: Installation Wizard & Setup

**Branch:** `modernize/11-setup-wizard`

**Goal:** Replace the PHP installation wizard with a modern Artisan-based setup and optional web wizard.

**Tasks:**
1. Create `php artisan ebot:install` interactive command that:
   - Tests MySQL connection
   - Runs migrations
   - Creates admin user
   - Generates `config/ebot.php` values → writes to `.env`
   - Outputs eBot Node.js `config.ini` template
   - Clears caches
2. Create a web-based setup wizard (Livewire) for non-CLI users, matching the 7-step flow:
   - Step 1: Database connection test
   - Step 2: Admin user creation
   - Step 3: eBot web panel configuration
   - Step 4: eBot Node.js config generation
   - Step 5: Manual steps documentation
   - Step 6: Finish (cache clear, remove wizard route)
3. Add setup detection middleware: if no admin user exists, redirect to wizard
4. Create Docker-based one-command setup: `docker compose up -d` → app ready

**Deliverable:** Fresh installation works via both CLI and web wizard. Docker setup works out of the box.

---

### Phase 12: Testing

**Branch:** `modernize/12-testing`

**Goal:** Comprehensive test suite replacing the minimal Symfony lime tests. Every feature and flow — both new and existing — must be covered by tests to ensure future contributions don't break functionality.

**Testing Philosophy:** Tests are not an afterthought. Every phase should include tests for its deliverables (noted as requirements here), and this phase fills any remaining gaps to achieve full coverage. The test suite serves as a living specification of all eBot-CS2-Web behavior.

**Tasks:**
1. **Unit tests** (Pest PHP):
   - `EncryptionService` — verify AES-CTR output matches existing JS implementation
   - `JwtService` — verify tokens validate against eBot Node.js
   - `MatchService` — status transitions, command formatting
   - `ToornamentService` — API request/response handling
   - `DemoService` — path construction, security (directory traversal prevention)
   - `HeatmapService` — coordinate transformations
   - Model factories and relationship tests

2. **Feature tests**:
   - Auth flow (login, SHA1→bcrypt upgrade, logout, remember me)
   - Match CRUD (create, edit, start, stop, archive, duplicate)
   - Server/Team/Season/Config CRUD
   - Demo download (file serving, traversal protection)
   - API JSON export endpoints (verify structure matches legacy format)
   - Toornament import/export
   - Language switching
   - Admin middleware (unauthorized access blocked)

3. **Browser tests** (Laravel Dusk):
   - Match creation flow
   - RCON terminal interaction
   - Live map rendering
   - Livewire component reactivity

4. **Compatibility tests**:
   - Verify JWT tokens generated by Laravel validate in a Node.js script using the same secret
   - Verify AES-CTR encrypted commands from Laravel JS decrypt correctly in Node.js
   - Verify database writes from Laravel don't break eBot Node.js reads

**Deliverable:** 80%+ code coverage. CI pipeline runs all tests.

---

### Phase 13: Cleanup & Migration Tooling

**Branch:** `modernize/13-cleanup`

**Goal:** Remove all Symfony code, create migration tooling for existing installations, and finalize deployment configuration.

**Tasks:**
1. Remove Symfony directories:
   - `apps/`, `lib/vendor/symfony/`, `plugins/`, `symfony` CLI script
   - `lib/model/doctrine/`, `lib/form/`, `lib/filter/`
   - `config/doctrine/`, `config/ProjectConfiguration.class.php`
   - `web/index.php` (old), `web/admin.php`, `web/installation/`
   - Old static assets (`web/css/`, `web/js/` — replaced by Vite-built assets)
2. Create `php artisan ebot:migrate-from-symfony` command that:
   - Reads existing `config/databases.yml` and `config/app_user.yml` → generates `.env`
   - Adds new Laravel tables to existing database (`sessions`, `cache`, `jobs`, etc.)
   - Adds bcrypt `password` column to `sf_guard_user` (users will be upgraded on next login)
   - Validates data integrity
3. Update `CLAUDE.md` with new architecture and commands
4. Update `README.md` with new installation instructions
5. Create deployment configs:
   - `Dockerfile` (production — PHP 8.4-FPM + Nginx)
   - `docker-compose.yml` (production)
   - `docker-compose.dev.yml` (development with hot reload)
   - `.github/workflows/ci.yml` (GitHub Actions: lint, test, build)
   - `.github/workflows/deploy.yml` (optional deployment pipeline)
6. Configure Laravel Pint for code style (PSR-12)
7. Configure Larastan (PHPStan for Laravel) for static analysis

**Deliverable:** Clean Laravel-only codebase. Existing installations can upgrade via migration command. CI/CD pipeline operational.

---

## 6. Database Migration Strategy

```
┌─────────────────────────────────────────────┐
│          Existing MySQL (ebotv3)            │
│                                             │
│  ┌─────────┐  ┌──────┐  ┌─────────┐       │
│  │ matchs  │  │ maps │  │ players │  ...   │  ← eBot Node.js reads/writes these
│  └─────────┘  └──────┘  └─────────┘       │
│                                             │
│  ┌──────────────┐  ┌───────────────┐       │
│  │ sf_guard_user│  │ sf_guard_*    │       │  ← Laravel reads, adds password column
│  └──────────────┘  └───────────────┘       │
│                                             │
│  ┌──────────┐  ┌───────┐  ┌──────┐        │
│  │ sessions │  │ cache │  │ jobs │         │  ← New Laravel-only tables
│  └──────────┘  └───────┘  └──────┘        │
└─────────────────────────────────────────────┘
```

**Rules:**
- Never rename existing tables or columns
- Never change column types on existing columns
- New columns get defaults so eBot Node.js ignores them
- New tables are Laravel-only (sessions, cache, jobs, password_reset_tokens, failed_jobs)
- The `sf_guard_user` table gets an additional `password_bcrypt` column; once all users have logged in and been upgraded, a future migration can drop the SHA1 columns

## 7. Risk Mitigation

| Risk | Mitigation |
|---|---|
| eBot Node.js incompatibility | Phase 12 includes explicit compatibility tests. JWT + AES encryption tested against Node.js. |
| Data loss during migration | Migration command is additive only. No destructive schema changes. Backup instructions provided. |
| Broken real-time features | Socket.IO client code is ported directly, not replaced. Same events, same encryption. |
| User password loss | SHA1→bcrypt upgrade is transparent and lazy (on login). Both hash columns coexist. |
| Missing functionality | Each phase targets feature parity, not feature expansion. New features are out of scope. |
| Performance regression | Eloquent eager loading replaces Doctrine DQL. Laravel query caching for stats pages. |

## 8. Out of Scope

The following are explicitly **not** part of this modernization:

- Changing the eBot Node.js server code
- Modifying the Socket.IO event protocol
- Adding new features (OAuth2, REST API v2, mobile app, etc.)
- Changing the database schema beyond additive columns/tables
- Migrating from MySQL to another database
- Rewriting the eBot Node.js bot
- Multi-tenancy or SaaS features

## 9. Success Criteria

1. All existing functionality works identically from the user's perspective
2. Existing eBot Node.js installations work with the new web panel without any changes
3. Existing databases migrate without data loss
4. All three languages (en, ru, cn) work
5. Test suite passes with 80%+ coverage
6. Docker-based setup works in under 5 minutes
7. No Symfony 1.4 code remains in the final codebase
8. PHP 8.4, MySQL 8.4 LTS, Laravel 12 are the minimum supported versions
