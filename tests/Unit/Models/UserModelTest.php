<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('User model', function () {
    it('uses sf_guard_user table', function () {
        expect((new User)->getTable())->toBe('sf_guard_user');
    });
});

describe('User getDisplayName()', function () {
    it('returns full name when first and last are set', function () {
        $user = new User(['first_name' => 'John', 'last_name' => 'Doe']);
        expect($user->getDisplayName())->toBe('John Doe');
    });

    it('falls back to username when name is empty', function () {
        $user = new User(['first_name' => '', 'last_name' => '', 'username' => 'jdoe']);
        expect($user->getDisplayName())->toBe('jdoe');
    });
});

describe('User isAdmin()', function () {
    it('returns true for super admin', function () {
        $user = new User(['is_super_admin' => true]);
        expect($user->isAdmin())->toBeTrue();
    });

    it('returns false for regular user', function () {
        $user = new User(['is_super_admin' => false]);
        expect($user->isAdmin())->toBeFalse();
    });
});

describe('User checkLegacyPassword()', function () {
    it('validates sha1 password correctly', function () {
        $salt = 'testsalt';
        $user = new User([
            'algorithm' => 'sha1',
            'salt' => $salt,
            'password' => sha1($salt.'secret'),
        ]);

        expect($user->checkLegacyPassword('secret'))->toBeTrue()
            ->and($user->checkLegacyPassword('wrong'))->toBeFalse();
    });

    it('returns false for bcrypt algorithm', function () {
        $user = new User(['algorithm' => 'bcrypt', 'salt' => null, 'password' => 'hashed']);
        expect($user->checkLegacyPassword('password'))->toBeFalse();
    });
});

describe('User factory', function () {
    it('creates a user with bcrypt password', function () {
        $user = User::factory()->create();
        expect($user->exists)->toBeTrue()
            ->and($user->algorithm)->toBe('bcrypt')
            ->and($user->is_active)->toBeTrue();
    });

    it('creates an admin user with admin() state', function () {
        $user = User::factory()->admin()->create();
        expect($user->is_super_admin)->toBeTrue();
    });

    it('creates a legacy sha1 user with legacySha1() state', function () {
        $user = User::factory()->legacySha1('mypassword')->create();
        expect($user->algorithm)->toBe('sha1')
            ->and($user->salt)->not->toBeNull()
            ->and($user->checkLegacyPassword('mypassword'))->toBeTrue();
    });
});
