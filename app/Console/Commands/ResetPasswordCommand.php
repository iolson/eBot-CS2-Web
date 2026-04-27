<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetPasswordCommand extends Command
{
    protected $signature = 'ebot:reset-password {username? : The username of the account to update}';

    protected $description = 'Reset the password for an eBot admin user';

    public function handle(): int
    {
        $username = $this->argument('username')
            ?? $this->ask('Username');

        if (! $username) {
            $this->error('Username is required.');

            return self::FAILURE;
        }

        $user = User::where('username', $username)->first();

        if (! $user) {
            $this->error("User '{$username}' not found.");

            return self::FAILURE;
        }

        $password = $this->secret('New password');

        if (! $password) {
            $this->error('Password cannot be empty.');

            return self::FAILURE;
        }

        $user->update([
            'password' => Hash::make($password),
            'algorithm' => 'bcrypt',
        ]);

        $this->info("Password updated for user '{$username}'.");

        return self::SUCCESS;
    }
}
