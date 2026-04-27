<?php

namespace App\Auth;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

/**
 * Custom user provider that authenticates against the sfGuard `sf_guard_user` table.
 *
 * On first login with a legacy SHA-1 password, transparently upgrades to bcrypt.
 */
class SfGuardUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by their username or email_address credential.
     *
     * sfGuard users log in with `username`, not `email`. We support both.
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        $query = $this->createModel()->newQuery()
            ->where('is_active', true);

        $login = $credentials['username'] ?? $credentials['email'] ?? null;

        if ($login) {
            $query->where(function ($q) use ($login) {
                $q->where('username', $login)
                  ->orWhere('email_address', $login);
            });
        }

        return $query->first();
    }

    /**
     * Validate credentials, transparently upgrading SHA-1 passwords to bcrypt.
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        /** @var User $user */
        $plain = $credentials['password'] ?? '';

        // Modern bcrypt password
        if ($user->algorithm === 'bcrypt') {
            return Hash::check($plain, $user->getAuthPassword());
        }

        // Legacy SHA-1: sha1($salt . $plain)
        if ($user->checkLegacyPassword($plain)) {
            $this->upgradePasswordToBcrypt($user, $plain);

            return true;
        }

        return false;
    }

    /**
     * Upgrade a legacy SHA-1 password to bcrypt in-place.
     */
    private function upgradePasswordToBcrypt(User $user, string $plain): void
    {
        $user->password  = Hash::make($plain);
        $user->algorithm = 'bcrypt';
        $user->salt      = null;
        $user->save();
    }
}
