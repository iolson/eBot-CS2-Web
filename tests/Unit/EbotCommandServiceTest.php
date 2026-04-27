<?php

use App\Models\Matchs;
use App\Services\AesCtrService;
use App\Services\EbotCommandService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

describe('EbotCommandService', function () {

    function makeService(): EbotCommandService
    {
        return new EbotCommandService(
            new AesCtrService,
            'http://localhost:12360',
        );
    }

    function makeMatch(string $authkey = 'test-auth-key-32chars-padding123', string $ip = '127.0.0.1:27015'): Matchs
    {
        $match = new Matchs;
        $match->id = 42;
        $match->config_authkey = $authkey;
        $match->ip = $ip;

        return $match;
    }

    it('sends a POST to the eBot server with encrypted payload', function () {
        Http::fake([
            'http://localhost:12360/match-command' => Http::response('', 200),
        ]);

        $service = makeService();
        $match = makeMatch();

        $result = $service->send($match, 'stop');

        expect($result)->toBeTrue();

        Http::assertSent(function (Request $request) {
            return $request->url() === 'http://localhost:12360/match-command'
                && $request->method() === 'POST'
                && isset($request['data']);
        });
    });

    it('returns false when the eBot server is unreachable', function () {
        Http::fake([
            'http://localhost:12360/match-command' => fn () => throw new ConnectionException('Connection refused'),
        ]);

        Log::spy();

        $service = makeService();
        $match = makeMatch();

        $result = $service->send($match, 'forcestart');

        expect($result)->toBeFalse();
    });

    it('returns false when the match has no authkey', function () {
        Log::spy();

        $service = makeService();
        $match = makeMatch('', '127.0.0.1:27015');

        $result = $service->send($match, 'stop');

        expect($result)->toBeFalse();
    });

    it('returns false when the match has no server IP', function () {
        Log::spy();

        $service = makeService();
        $match = makeMatch('some-authkey', '');

        $result = $service->send($match, 'stop');

        expect($result)->toBeFalse();
    });

    it('returns false on a non-2xx HTTP response', function () {
        Http::fake([
            'http://localhost:12360/match-command' => Http::response('Server Error', 500),
        ]);

        Log::spy();

        $service = makeService();
        $match = makeMatch();

        $result = $service->send($match, 'pauseunpause');

        expect($result)->toBeFalse();
    });

    it('builds an encrypted JSON payload for the browser Socket.IO client', function () {
        $service = makeService();
        $match = makeMatch('my-secret-key-256bits-padded-here', '10.0.0.1:27015');

        $payload = $service->buildPayload($match, 'forceknife');

        $decoded = json_decode($payload, true);
        expect($decoded)->toBeArray()->toHaveCount(2);
        expect($decoded[1])->toBe('10.0.0.1:27015');
        // Encrypted data should be non-empty and differ from the plaintext
        expect($decoded[0])->not->toBe('42 forceknife 10.0.0.1:27015');
        expect($decoded[0])->not->toBeEmpty();
    });

    it('returns a neutral payload when match data is missing', function () {
        $service = makeService();
        $match = makeMatch('', '');

        $payload = $service->buildPayload($match, 'stop');
        $decoded = json_decode($payload, true);

        expect($decoded)->toEqual(['', '']);
    });
});
