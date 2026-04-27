<?php

return [

    /*
    |--------------------------------------------------------------------------
    | eBot Node.js Server Connection
    |--------------------------------------------------------------------------
    |
    | The eBot Node.js server manages game servers via RCON and writes match
    | data directly to the shared MySQL database. The web panel connects to
    | it via Socket.IO for real-time updates and command dispatch.
    |
    */

    'ip' => env('EBOT_IP', '127.0.0.1'),
    'port' => env('EBOT_PORT', 12360),
    'websocket_url' => env('EBOT_WEBSOCKET_URL', 'http://localhost:12360'),

    /*
    |--------------------------------------------------------------------------
    | WebSocket Authentication
    |--------------------------------------------------------------------------
    |
    | JWT tokens signed with this key authenticate Socket.IO connections.
    | This MUST match the key configured in the eBot Node.js server.
    | Algorithm: HS256, TTL: 31 days.
    |
    */

    'websocket_secret_key' => env('EBOT_WEBSOCKET_SECRET_KEY', 'generatestrongsecretkey'),

    /*
    |--------------------------------------------------------------------------
    | File Paths
    |--------------------------------------------------------------------------
    |
    | Paths to eBot Node.js server files. These can be absolute or relative
    | to the project root. The web panel reads log files and serves demo
    | downloads from these locations.
    |
    */

    'log_match_path' => env('EBOT_LOG_MATCH_PATH', '../../ebot-csgo/logs/log_match'),
    'log_match_admin_path' => env('EBOT_LOG_MATCH_ADMIN_PATH', '../../ebot-csgo/logs/log_match_admin'),
    'demo_path' => env('EBOT_DEMO_PATH', '../../ebot-csgo/demos'),

    /*
    |--------------------------------------------------------------------------
    | Display Mode
    |--------------------------------------------------------------------------
    |
    | "lan" displays server IPs and shows all matches on the homepage.
    | "net" hides server IPs and only shows started matches on the homepage.
    |
    */

    'mode' => env('EBOT_MODE', 'net'),

    /*
    |--------------------------------------------------------------------------
    | Match Defaults
    |--------------------------------------------------------------------------
    |
    | Default values applied when creating new matches.
    |
    */

    'default_max_round' => (int) env('EBOT_DEFAULT_MAX_ROUND', 12),
    'default_rules' => env('EBOT_DEFAULT_RULES', 'esl_pro_tour'),
    'default_overtime_enable' => (bool) env('EBOT_DEFAULT_OVERTIME_ENABLE', true),
    'default_overtime_max_round' => (int) env('EBOT_DEFAULT_OVERTIME_MAX_ROUND', 3),
    'default_overtime_startmoney' => (int) env('EBOT_DEFAULT_OVERTIME_STARTMONEY', 12500),

    /*
    |--------------------------------------------------------------------------
    | Demo Downloads
    |--------------------------------------------------------------------------
    */

    'demo_download' => (bool) env('EBOT_DEMO_DOWNLOAD', true),

    /*
    |--------------------------------------------------------------------------
    | Page Refresh
    |--------------------------------------------------------------------------
    |
    | Auto-refresh interval in seconds for match list pages. Set to 0 to disable.
    |
    */

    'refresh_time' => (int) env('EBOT_REFRESH_TIME', 30),

    /*
    |--------------------------------------------------------------------------
    | Maps
    |--------------------------------------------------------------------------
    |
    | Available CS2 maps for match creation.
    |
    */

    // ESL Pro Tour 2026 active duty map pool (Valve Active Duty Map Group).
    // de_vertigo is excluded — not in the ESL 2026 pool.
    'maps' => [
        'de_dust2',
        'de_inferno',
        'de_overpass',
        'de_nuke',
        'de_ancient',
        'de_anubis',
        'de_mirage',
    ],

    /*
    |--------------------------------------------------------------------------
    | Toornament Integration
    |--------------------------------------------------------------------------
    |
    | OAuth2 client credentials for the Toornament tournament platform API.
    |
    */

    'toornament' => [
        'id' => env('TOORNAMENT_ID'),
        'secret' => env('TOORNAMENT_SECRET'),
        'api_key' => env('TOORNAMENT_API_KEY'),
        'plugin_key' => env('TOORNAMENT_PLUGIN_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Version
    |--------------------------------------------------------------------------
    */

    'version' => '4.0 - CS2',

];
