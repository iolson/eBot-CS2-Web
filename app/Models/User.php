<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Authentication user backed by the sfGuard `sf_guard_user` table.
 *
 * Password migration: legacy passwords are SHA-1 hashed as sha1($salt . $plaintext).
 * The custom SfGuardUserProvider handles transparent upgrade to bcrypt on login.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'sf_guard_user';

    protected $fillable = [
        'first_name', 'last_name', 'email_address',
        'username', 'algorithm', 'salt', 'password',
        'is_active', 'is_super_admin',
    ];

    protected $hidden = [
        'password', 'salt',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_super_admin' => 'boolean',
            'last_login' => 'datetime',
        ];
    }

    public function getAuthPassword(): string
    {
        return (string) $this->password;
    }

    public function getDisplayName(): string
    {
        $full = trim("{$this->first_name} {$this->last_name}");

        return $full ?: $this->username;
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    /** Check SHA-1 password as used by sfGuard: sha1($salt . $plaintext) */
    public function checkLegacyPassword(string $plain): bool
    {
        return $this->algorithm === 'sha1'
            && hash_equals($this->password, sha1($this->salt.$plain));
    }
}
