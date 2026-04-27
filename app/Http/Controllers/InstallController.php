<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class InstallController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (config('app.installed')) {
            return redirect('/');
        }

        return view('install.index');
    }

    public function store(Request $request): RedirectResponse
    {
        if (config('app.installed')) {
            return redirect('/');
        }

        $data = $request->validate([
            'db_host' => ['required', 'string'],
            'db_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'db_name' => ['required', 'string'],
            'db_user' => ['required', 'string'],
            'db_pass' => ['nullable', 'string'],
            'ebot_url' => ['required', 'url'],
            'ebot_secret' => ['required', 'string', 'min:16'],
            'ebot_mode' => ['required', 'in:net,lan'],
            'admin_username' => ['required', 'string', 'min:3'],
            'admin_email' => ['required', 'email'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Test DB connection before writing anything
        config([
            'database.connections.install_test' => [
                'driver' => 'mysql',
                'host' => $data['db_host'],
                'port' => $data['db_port'],
                'database' => $data['db_name'],
                'username' => $data['db_user'],
                'password' => $data['db_pass'] ?? '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
        ]);

        try {
            DB::connection('install_test')->getPdo();
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'db_host' => 'Could not connect to database: '.$e->getMessage(),
            ]);
        }

        // Write .env
        $this->writeEnv([
            'DB_HOST' => $data['db_host'],
            'DB_PORT' => $data['db_port'],
            'DB_DATABASE' => $data['db_name'],
            'DB_USERNAME' => $data['db_user'],
            'DB_PASSWORD' => $data['db_pass'] ?? '',
            'EBOT_WEBSOCKET_URL' => $data['ebot_url'],
            'EBOT_WEBSOCKET_SECRET_KEY' => $data['ebot_secret'],
            'EBOT_MODE' => $data['ebot_mode'],
        ]);

        // Run migrations against the real connection now that .env is written
        Artisan::call('migrate', ['--force' => true]);

        // Create admin user
        User::updateOrCreate(
            ['username' => $data['admin_username']],
            [
                'email_address' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'algorithm' => 'bcrypt',
                'is_super_admin' => true,
                'is_active' => true,
            ]
        );

        // Mark installed
        $this->writeEnv(['APP_INSTALLED' => 'true']);

        return redirect('/')->with('success', 'eBot CS2 Web installed successfully! Please log in.');
    }

    /** @param array<string,string|int> $values */
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
