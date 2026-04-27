<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Models\Server;
use App\Models\User;

describe('Admin server controller', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
    });

    it('shows server index', function () {
        $this->actingAs($this->admin)
            ->get(route('admin.servers.index'))
            ->assertOk();
    });

    it('creates a single server', function () {
        $this->actingAs($this->admin)
            ->post(route('admin.servers.store'), [
                'ip_port' => '192.168.1.1:27015',
                'rcon'    => 'secret',
            ])
            ->assertRedirect(route('admin.servers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('servers', ['ip' => '192.168.1.1:27015']);
    });

    it('creates multiple servers from ip range', function () {
        $this->actingAs($this->admin)
            ->post(route('admin.servers.store'), [
                'ip_port' => '192.168.1.1-3:27015',
                'rcon'    => 'secret',
            ])
            ->assertRedirect(route('admin.servers.index'));

        $this->assertEquals(3, Server::count());
    });

    it('creates multiple servers from port range', function () {
        $this->actingAs($this->admin)
            ->post(route('admin.servers.store'), [
                'ip_port' => '192.168.1.1:27015-27017',
                'rcon'    => 'secret',
            ])
            ->assertRedirect(route('admin.servers.index'));

        $this->assertEquals(3, Server::count());
    });

    it('rejects invalid ip_port format', function () {
        $this->actingAs($this->admin)
            ->post(route('admin.servers.store'), [
                'ip_port' => 'not-valid',
                'rcon'    => 'secret',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');
    });

    it('does not duplicate existing server', function () {
        Server::factory()->create(['ip' => '192.168.1.1:27015']);

        $this->actingAs($this->admin)
            ->post(route('admin.servers.store'), [
                'ip_port' => '192.168.1.1:27015',
                'rcon'    => 'newpass',
            ]);

        $this->assertEquals(1, Server::count());
    });

    it('deletes an idle server', function () {
        $server = Server::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.servers.destroy', $server))
            ->assertRedirect(route('admin.servers.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($server);
    });
});
