<?php

use App\Services\AesCtrService;

describe('AesCtrService', function () {
    $aes = fn () => new AesCtrService;

    it('encrypts and decrypts to original plaintext (128-bit)', function () use ($aes) {
        $service = $aes();
        $plaintext = 'Hello, eBot!';
        $password = 'testpassword123';

        $encrypted = $service->encrypt($plaintext, $password, 128);
        $decrypted = $service->decrypt($encrypted, $password, 128);

        expect($decrypted)->toBe($plaintext);
    });

    it('encrypts and decrypts to original plaintext (256-bit)', function () use ($aes) {
        $service = $aes();
        $plaintext = '1 start 192.168.1.1:27015';
        $password = 'match-auth-key-256';

        $encrypted = $service->encrypt($plaintext, $password, 256);
        $decrypted = $service->decrypt($encrypted, $password, 256);

        expect($decrypted)->toBe($plaintext);
    });

    it('returns empty string for unsupported bit length', function () use ($aes) {
        $result = $aes()->encrypt('test', 'pass', 64);
        expect($result)->toBe('');
    });

    it('produces base64-encoded output', function () use ($aes) {
        $encrypted = $aes()->encrypt('data', 'key', 256);
        expect(base64_decode($encrypted, true))->not->toBeFalse();
    });

    it('each encryption produces different ciphertext (nonce randomness)', function () use ($aes) {
        $service = $aes();
        $enc1 = $service->encrypt('same text', 'same key', 256);
        $enc2 = $service->encrypt('same text', 'same key', 256);

        // Different nonces mean different ciphertexts (probabilistically)
        expect($enc1)->not->toBe($enc2);
    });

    it('handles empty plaintext', function () use ($aes) {
        $service = $aes();
        $encrypted = $service->encrypt('', 'key', 256);
        $decrypted = $service->decrypt($encrypted, 'key', 256);

        expect($decrypted)->toBe('');
    });

    it('handles unicode/multibyte characters', function () use ($aes) {
        $service = $aes();
        $plaintext = 'тест матч';
        $encrypted = $service->encrypt($plaintext, 'key', 256);
        $decrypted = $service->decrypt($encrypted, 'key', 256);

        expect($decrypted)->toBe($plaintext);
    });

    it('matches match command format: "id action ip"', function () use ($aes) {
        $service = $aes();
        $matchId = '42';
        $action = 'start';
        $ip = '192.168.1.100:27015';
        $authKey = 'abc123secretkey';
        $plaintext = "{$matchId} {$action} {$ip}";

        $encrypted = $service->encrypt($plaintext, $authKey, 256);
        $decrypted = $service->decrypt($encrypted, $authKey, 256);

        expect($decrypted)->toBe($plaintext);
    });
});
