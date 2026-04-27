<?php

use App\Models\Server;
use Illuminate\Database\Eloquent\Relations\HasMany;

describe('Server model', function () {
    it('uses the servers table', function () {
        expect((new Server)->getTable())->toBe('servers');
    });
});

describe('Server getDisplayIp()', function () {
    it('returns ip in lan mode', function () {
        config(['ebot.mode' => 'lan']);

        $server = new Server(['ip' => '192.168.1.10:27015', 'tv_ip' => '1.2.3.4']);

        expect($server->getDisplayIp())->toBe('192.168.1.10:27015');
    });

    it('returns tv_ip in net mode when set', function () {
        config(['ebot.mode' => 'net']);

        $server = new Server(['ip' => '192.168.1.10:27015', 'tv_ip' => '1.2.3.4']);

        expect($server->getDisplayIp())->toBe('1.2.3.4');
    });

    it('falls back to ip in net mode when tv_ip is null', function () {
        config(['ebot.mode' => 'net']);

        $server = new Server(['ip' => '192.168.1.10:27015', 'tv_ip' => null]);

        expect($server->getDisplayIp())->toBe('192.168.1.10:27015');
    });
});

describe('Server factory', function () {
    it('creates a server with factory', function () {
        $server = Server::factory()->make();

        expect($server->ip)->toBeString()
            ->and($server->rcon)->toBeString();
    });
});

describe('Server relationships', function () {
    it('has many matches', function () {
        $server = new Server;

        expect($server->matches())->toBeInstanceOf(HasMany::class);
    });
});
