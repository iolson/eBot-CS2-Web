<?php

use App\Services\JwtService;

describe('JwtService', function () {
    $jwt = fn () => new JwtService('test-secret-key');

    it('throws when secret is empty', function () {
        expect(fn () => new JwtService(''))->toThrow(RuntimeException::class);
    });

    it('encodes and decodes a payload round-trip', function () use ($jwt) {
        $payload = ['foo' => 'bar', 'num' => 42];
        $token   = $jwt()->encode($payload);
        $decoded = $jwt()->decode($token);

        expect($decoded['foo'])->toBe('bar')
            ->and($decoded['num'])->toBe(42);
    });

    it('generates a token with 3 dot-separated segments', function () use ($jwt) {
        $token = $jwt()->encode(['test' => true]);
        expect(substr_count($token, '.'))->toBe(2);
    });

    it('creates admin token with admin=true and user field', function () use ($jwt) {
        $token   = $jwt()->forAdmin('johndoe');
        $decoded = $jwt()->decode($token);

        expect($decoded['admin'])->toBeTrue()
            ->and($decoded['user'])->toBe('johndoe');
    });

    it('creates public token with admin=false', function () use ($jwt) {
        $token   = $jwt()->forPublic();
        $decoded = $jwt()->decode($token);

        expect($decoded['admin'])->toBeFalse();
    });

    it('adds exp claim automatically', function () use ($jwt) {
        $before  = time();
        $token   = $jwt()->encode(['x' => 1]);
        $decoded = $jwt()->decode($token);
        $after   = time();

        // exp should be ~31 days from now
        $ttl = 60 * 60 * 24 * 31;
        expect($decoded['exp'])->toBeGreaterThanOrEqual($before + $ttl)
            ->and($decoded['exp'])->toBeLessThanOrEqual($after + $ttl);
    });

    it('throws on tampered signature', function () use ($jwt) {
        $token  = $jwt()->encode(['data' => 'secure']);
        $parts  = explode('.', $token);
        $parts[2] = 'invalidsig';
        $tampered = implode('.', $parts);

        expect(fn () => $jwt()->decode($tampered))->toThrow(RuntimeException::class);
    });

    it('throws on incomplete token', function () use ($jwt) {
        expect(fn () => $jwt()->decode('only.two'))->toThrow(RuntimeException::class);
    });

    it('produces HS256 header', function () use ($jwt) {
        $token  = $jwt()->encode(['x' => 1]);
        $header = json_decode(base64_decode(strtr(explode('.', $token)[0], '-_', '+/')), true);

        expect($header['alg'])->toBe('HS256')
            ->and($header['typ'])->toBe('JWT');
    });
});
