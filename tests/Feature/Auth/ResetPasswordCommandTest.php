<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('ebot:reset-password command', function () {
    it('resets the password for an existing user', function () {
        $user = User::factory()->create(['username' => 'testuser', 'algorithm' => 'sha1']);

        $this->artisan('ebot:reset-password', ['username' => 'testuser'])
            ->expectsQuestion('New password', 'newSecret123')
            ->assertExitCode(0);

        $user->refresh();
        expect($user->algorithm)->toBe('bcrypt');
        expect(Hash::check('newSecret123', $user->password))->toBeTrue();
    });

    it('fails when the user does not exist', function () {
        $this->artisan('ebot:reset-password', ['username' => 'nobody'])
            ->assertExitCode(1);
    });

    it('prompts for username when not provided as argument', function () {
        $user = User::factory()->create(['username' => 'prompted_user']);

        $this->artisan('ebot:reset-password')
            ->expectsQuestion('Username', 'prompted_user')
            ->expectsQuestion('New password', 'anotherPass!')
            ->assertExitCode(0);

        $user->refresh();
        expect(Hash::check('anotherPass!', $user->password))->toBeTrue();
    });
});
