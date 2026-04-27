<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('Login page', function () {
    it('shows the login form', function () {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Sign In');
    });

    it('redirects authenticated users away from login', function () {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect();
    });
});

describe('Login with bcrypt password', function () {
    it('authenticates with valid credentials', function () {
        $user = User::factory()->create([
            'username' => 'admin',
            'password' => Hash::make('secret123'),
            'algorithm' => 'bcrypt',
        ]);

        $this->post(route('login'), [
            'username' => 'admin',
            'password' => 'secret123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    });

    it('rejects invalid password', function () {
        User::factory()->create([
            'username' => 'admin',
            'password' => Hash::make('correct'),
            'algorithm' => 'bcrypt',
        ]);

        $this->post(route('login'), [
            'username' => 'admin',
            'password' => 'wrong',
        ])->assertRedirect()
          ->assertSessionHasErrors('username');

        $this->assertGuest();
    });

    it('rejects inactive users', function () {
        User::factory()->inactive()->create([
            'username' => 'inactive',
            'password' => Hash::make('password'),
            'algorithm' => 'bcrypt',
        ]);

        $this->post(route('login'), [
            'username' => 'inactive',
            'password' => 'password',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    });
});

describe('Login with legacy SHA-1 password', function () {
    it('authenticates and upgrades password to bcrypt', function () {
        $salt      = 'testsalt123';
        $plaintext = 'legacypass';
        $sha1hash  = sha1($salt . $plaintext);

        $user = User::factory()->create([
            'username'  => 'legacyuser',
            'algorithm' => 'sha1',
            'salt'      => $salt,
            'password'  => $sha1hash,
        ]);

        $this->post(route('login'), [
            'username' => 'legacyuser',
            'password' => $plaintext,
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);

        $user->refresh();
        expect($user->algorithm)->toBe('bcrypt')
            ->and($user->salt)->toBeNull()
            ->and(Hash::check($plaintext, $user->password))->toBeTrue();
    });

    it('rejects wrong password even for sha1 users', function () {
        $user = User::factory()->legacySha1('correct')->create(['username' => 'sha1user']);

        $this->post(route('login'), [
            'username' => 'sha1user',
            'password' => 'wrong',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    });
});

describe('Login can authenticate by email_address', function () {
    it('authenticates using email_address field', function () {
        $user = User::factory()->create([
            'email_address' => 'admin@example.com',
            'password'      => Hash::make('password'),
            'algorithm'     => 'bcrypt',
        ]);

        $this->post(route('login'), [
            'username' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    });
});

describe('Logout', function () {
    it('logs out authenticated users', function () {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    });
});
