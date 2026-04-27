<?php

namespace App\Services;

use RuntimeException;

/**
 * JWT service compatible with eBot Node.js WebSocket authentication.
 *
 * Generates HS256 JWTs with a 31-day TTL and 10-second leeway.
 * Payload format: {admin: bool, user?: string, exp: int}
 *
 * Must remain byte-for-byte compatible with the JWT implementation in
 * legacy/lib/JWT.class.php (adhocore/jwt style HS256).
 */
class JwtService
{
    private const ALGO    = 'HS256';
    private const TTL     = 60 * 60 * 24 * 31; // 31 days in seconds
    private const LEEWAY  = 10;                 // seconds of clock skew tolerance

    public function __construct(private readonly string $secret)
    {
        if (empty($secret)) {
            throw new RuntimeException('JWT secret key cannot be empty.');
        }
    }

    /**
     * Generate a JWT token for an authenticated admin user.
     */
    public function forAdmin(string $username): string
    {
        return $this->encode([
            'admin' => true,
            'user'  => $username,
        ]);
    }

    /**
     * Generate a JWT token for an unauthenticated (public) viewer.
     */
    public function forPublic(): string
    {
        return $this->encode(['admin' => false]);
    }

    /**
     * Encode payload as a HS256 JWT.
     */
    public function encode(array $payload): string
    {
        if (! isset($payload['exp'])) {
            $payload['exp'] = time() + self::TTL;
        }

        $header    = $this->base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => self::ALGO], JSON_THROW_ON_ERROR));
        $body      = $this->base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR));
        $signature = $this->base64UrlEncode(hash_hmac('sha256', "{$header}.{$body}", $this->secret, true));

        return "{$header}.{$body}.{$signature}";
    }

    /**
     * Decode and verify a JWT token.
     *
     * @throws RuntimeException on invalid or expired token
     */
    public function decode(string $token): array
    {
        $parts = explode('.', $token, 3);

        if (count($parts) !== 3) {
            throw new RuntimeException('Invalid JWT: incomplete segments.');
        }

        [$encodedHeader, $encodedPayload, $encodedSig] = $parts;

        $expected = $this->base64UrlEncode(hash_hmac('sha256', "{$encodedHeader}.{$encodedPayload}", $this->secret, true));

        if (! hash_equals($expected, $encodedSig)) {
            throw new RuntimeException('Invalid JWT: signature mismatch.');
        }

        $payload = json_decode(base64_decode(strtr($encodedPayload, '-_', '+/')), true, 512, JSON_THROW_ON_ERROR);

        $now = time();
        if (isset($payload['exp']) && $now >= $payload['exp'] + self::LEEWAY) {
            throw new RuntimeException('Invalid JWT: token expired.');
        }

        return $payload;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
