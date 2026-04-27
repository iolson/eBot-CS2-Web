<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Toornament tournament platform API client.
 *
 * Uses OAuth2 client credentials (scopes: organizer:admin organizer:view organizer:result).
 * Access tokens are cached for 24 hours matching the legacy ToornamentAPI.class.php behaviour.
 *
 * @see legacy/lib/ToornamentAPI.class.php
 */
class ToornamentService
{
    private const BASE_URL   = 'https://api.toornament.com/';
    private const TOKEN_TTL  = 60 * 60 * 24; // 24 hours in seconds
    private const CACHE_KEY  = 'toornament_access_token';

    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $apiKey,
    ) {}

    public function isConfigured(): bool
    {
        return ! empty($this->clientId) && ! empty($this->clientSecret) && ! empty($this->apiKey);
    }

    // -------------------------------------------------------------------------
    // Tournament browsing
    // -------------------------------------------------------------------------

    /**
     * List all tournaments accessible to this client.
     */
    public function getTournaments(): array
    {
        return $this->get('v1/tournaments', [], true);
    }

    /**
     * List matches in a tournament (with games).
     */
    public function getTournamentMatches(string $tournamentId): array
    {
        return $this->get("v1/tournaments/{$tournamentId}/matches", ['with_games' => 1], true);
    }

    /**
     * Get a single match's data including games.
     */
    public function getMatch(string $tournamentId, string $matchId): array
    {
        return $this->get("v1/tournaments/{$tournamentId}/matches/{$matchId}", ['with_games' => 1], true);
    }

    /**
     * Get a stage from a tournament.
     */
    public function getStage(string $tournamentId, string $stageNumber): array
    {
        return $this->get("v1/tournaments/{$tournamentId}/stages/{$stageNumber}", [], true);
    }

    // -------------------------------------------------------------------------
    // Result export
    // -------------------------------------------------------------------------

    /**
     * Get the current result record for a specific game.
     */
    public function getGameResult(string $tournamentId, string $matchId, int $gameId): array
    {
        return $this->get(
            "v1/tournaments/{$tournamentId}/matches/{$matchId}/games/{$gameId}/result",
            [],
            true,
        );
    }

    /**
     * Push the result for a specific game.
     */
    public function putGameResult(string $tournamentId, string $matchId, int $gameId, array $result): array
    {
        return $this->put(
            "v1/tournaments/{$tournamentId}/matches/{$matchId}/games/{$gameId}/result",
            $result,
            true,
        );
    }

    /**
     * Update game metadata (e.g. map name).
     */
    public function patchGame(string $tournamentId, string $matchId, int $gameId, array $data): array
    {
        return $this->patch(
            "v1/tournaments/{$tournamentId}/matches/{$matchId}/games/{$gameId}",
            $data,
            true,
        );
    }

    // -------------------------------------------------------------------------
    // HTTP helpers
    // -------------------------------------------------------------------------

    private function get(string $uri, array $params = [], bool $needOAuth = false): array
    {
        $response = Http::withHeaders($this->headers($needOAuth))
            ->get(self::BASE_URL . $uri, $params);

        if (! $response->successful()) {
            throw new RuntimeException("Toornament GET {$uri} returned HTTP {$response->status()}");
        }

        return $response->json() ?? [];
    }

    private function put(string $uri, array $body = [], bool $needOAuth = false): array
    {
        $response = Http::withHeaders($this->headers($needOAuth))
            ->put(self::BASE_URL . $uri, $body);

        if (! $response->successful()) {
            throw new RuntimeException("Toornament PUT {$uri} returned HTTP {$response->status()}");
        }

        return $response->json() ?? [];
    }

    private function patch(string $uri, array $body = [], bool $needOAuth = false): array
    {
        $response = Http::withHeaders($this->headers($needOAuth))
            ->patch(self::BASE_URL . $uri, $body);

        if (! $response->successful()) {
            throw new RuntimeException("Toornament PATCH {$uri} returned HTTP {$response->status()}");
        }

        return $response->json() ?? [];
    }

    private function headers(bool $needOAuth): array
    {
        $headers = ['X-Api-Key' => $this->apiKey];

        if ($needOAuth) {
            $headers['Authorization'] = 'Bearer ' . $this->accessToken();
        }

        return $headers;
    }

    /**
     * Return a cached OAuth2 access token, refreshing if expired.
     */
    private function accessToken(): string
    {
        return Cache::remember(self::CACHE_KEY, self::TOKEN_TTL, function () {
            $response = Http::asForm()->post(self::BASE_URL . 'oauth/v2/token', [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope'         => 'organizer:admin organizer:view organizer:result',
            ]);

            if (! $response->successful()) {
                throw new RuntimeException('Toornament OAuth2 token request failed: HTTP ' . $response->status());
            }

            return $response->json('access_token');
        });
    }
}
