<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    protected $signature = 'ebot:install';

    protected $description = 'Run the eBot CS2 Web first-time installation wizard';

    public function handle(): int
    {
        $this->newLine();
        $this->line('  <fg=yellow;options=bold>eBot CS2 Web — Installation Wizard</>');
        $this->line('  ────────────────────────────────────');
        $this->newLine();

        // ── 1. App key ────────────────────────────────────────────────────────
        if (empty(config('app.key'))) {
            $this->info('Generating application key…');
            $this->call('key:generate');
        } else {
            $this->line('  ✓ Application key already set.');
        }

        // ── 2. Database ───────────────────────────────────────────────────────
        $this->newLine();
        $this->line('  <options=bold>Database</>');

        $dbHost = $this->ask('  DB host', env('DB_HOST', '127.0.0.1'));
        $dbPort = $this->ask('  DB port', env('DB_PORT', '3306'));
        $dbName = $this->ask('  DB name', env('DB_DATABASE', 'ebotv3'));
        $dbUser = $this->ask('  DB username', env('DB_USERNAME', 'root'));
        $dbPass = $this->secret('  DB password');

        $this->writeEnv([
            'DB_HOST' => $dbHost,
            'DB_PORT' => $dbPort,
            'DB_DATABASE' => $dbName,
            'DB_USERNAME' => $dbUser,
            'DB_PASSWORD' => $dbPass ?? '',
        ]);

        // Test connection
        config([
            'database.connections.mysql.host' => $dbHost,
            'database.connections.mysql.port' => $dbPort,
            'database.connections.mysql.database' => $dbName,
            'database.connections.mysql.username' => $dbUser,
            'database.connections.mysql.password' => $dbPass ?? '',
        ]);

        try {
            DB::connection()->getPdo();
            $this->line('  ✓ Database connection successful.');
        } catch (\Throwable $e) {
            $this->error('  ✗ Could not connect to database: '.$e->getMessage());
            $this->line('  Please fix your DB credentials in .env and re-run ebot:install.');

            return self::FAILURE;
        }

        // ── 3. Run migrations ─────────────────────────────────────────────────
        if ($this->confirm('  Run database migrations now?', true)) {
            $this->call('migrate', ['--force' => true]);
        }

        // ── 4. eBot Node.js server ────────────────────────────────────────────
        $this->newLine();
        $this->line('  <options=bold>eBot Node.js Server</>');

        $ebotUrl = $this->ask('  WebSocket URL (eBot Node.js)', env('EBOT_WEBSOCKET_URL', 'http://localhost:12360'));
        $ebotSecret = $this->ask('  WebSocket JWT secret (must match eBot config)', env('EBOT_WEBSOCKET_SECRET_KEY') ?: Str::random(48));

        $this->writeEnv([
            'EBOT_WEBSOCKET_URL' => $ebotUrl,
            'EBOT_WEBSOCKET_SECRET_KEY' => $ebotSecret,
        ]);

        $this->line('  ✓ eBot connection settings saved.');

        // ── 5. Display mode ───────────────────────────────────────────────────
        $this->newLine();
        $this->line('  <options=bold>Display Mode</>');
        $this->line('    <fg=gray>lan</> — shows server IPs on the frontend (internal/LAN use)');
        $this->line('    <fg=gray>net</> — hides server IPs (public-facing installation)');

        $mode = $this->choice('  Display mode', ['net', 'lan'], 0);
        $this->writeEnv(['EBOT_MODE' => $mode]);

        // ── 6. Admin user ─────────────────────────────────────────────────────
        $this->newLine();
        $this->line('  <options=bold>Admin Account</>');

        if ($this->confirm('  Create an admin user now?', true)) {
            $username = $this->ask('  Username');
            $email = $this->ask('  Email address');
            $password = $this->secret('  Password');

            if ($username && $password) {
                User::updateOrCreate(
                    ['username' => $username],
                    [
                        'email_address' => $email ?? $username.'@localhost',
                        'password' => Hash::make($password),
                        'algorithm' => 'bcrypt',
                        'is_super_admin' => true,
                        'is_active' => true,
                    ]
                );
                $this->line("  ✓ Admin user '{$username}' created.");
            }
        }

        // ── 7. Mark installed ─────────────────────────────────────────────────
        $this->writeEnv(['APP_INSTALLED' => 'true']);

        $this->newLine();
        $this->line('  <fg=green;options=bold>Installation complete!</>');
        $this->line('  Visit your application URL to get started.');
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Write/update key=value pairs in the .env file.
     *
     * @param  array<string,string>  $values
     */
    private function writeEnv(array $values): void
    {
        $path = base_path('.env');

        if (! file_exists($path)) {
            copy(base_path('.env.example'), $path);
        }

        $content = file_get_contents($path);

        foreach ($values as $key => $value) {
            $quoted = $this->quoteEnvValue((string) $value);
            $line = "{$key}={$quoted}";

            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", $line, $content);
            } else {
                $content .= PHP_EOL.$line;
            }
        }

        file_put_contents($path, $content);
    }

    private function quoteEnvValue(string $value): string
    {
        if ($value === '' || preg_match('/\s/', $value)) {
            return '"'.addslashes($value).'"';
        }

        return $value;
    }
}
