<?php

describe('Install wizard', function () {
    it('redirects to install when not installed', function () {
        config(['app.installed' => false]);

        $this->get('/')->assertRedirect('/install');
    });

    it('shows the install page when not installed', function () {
        config(['app.installed' => false]);

        $this->get('/install')->assertOk()->assertSee('Install eBot CS2 Web');
    });

    it('redirects away from install when already installed', function () {
        config(['app.installed' => true]);

        $this->get('/install')->assertRedirect('/');
    });

    it('does not redirect normal routes when installed', function () {
        config(['app.installed' => true]);

        $this->get('/login')->assertOk();
    });
});

describe('ebot:install Artisan command', function () {
    it('reports success and creates admin user', function () {
        $this->artisan('ebot:install')
            ->expectsQuestion('  DB host', '127.0.0.1')
            ->expectsQuestion('  DB port', '3306')
            ->expectsQuestion('  DB name', 'ebotv3_test')
            ->expectsQuestion('  DB username', 'root')
            ->expectsQuestion('  DB password', 'root')
            ->expectsConfirmation('  Run database migrations now?', 'no')
            ->expectsQuestion('  WebSocket URL (eBot Node.js)', 'http://localhost:12360')
            ->expectsQuestion('  WebSocket JWT secret (must match eBot config)', 'supersecretkey1234567890abcdef123456789012345678')
            ->expectsChoice('  Display mode', 'net', ['net', 'lan'])
            ->expectsConfirmation('  Create an admin user now?', 'yes')
            ->expectsQuestion('  Username', 'testadmin')
            ->expectsQuestion('  Email address', 'admin@localhost')
            ->expectsQuestion('  Password', 'password123')
            ->assertExitCode(0);

        $this->assertDatabaseHas('sf_guard_user', [
            'username' => 'testadmin',
            'is_super_admin' => true,
        ]);
    });
});
