<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * start.gg (formerly smash.gg) GraphQL API client.
 *
 * Uses a personal Bearer token (no OAuth2 flow).
 * Rate limit: 80 requests / 60 seconds, max 1000 objects per request.
 *
 * @see https://developer.start.gg/docs/intro
 */
class StartGgService
{
    private const API_URL = 'https://api.start.gg/gql/alpha';

    public function __construct(
        private readonly string $token,
    ) {}

    public function isConfigured(): bool
    {
        return ! empty($this->token);
    }

    /**
     * List tournaments owned by the authenticated user.
     */
    public function getTournamentsByOwner(int $page = 1): array
    {
        $result = $this->query('
            query TournamentsByOwner($page: Int!) {
                currentUser {
                    tournaments(query: { page: $page, perPage: 25 }) {
                        nodes {
                            id
                            name
                            slug
                        }
                    }
                }
            }
        ', ['page' => $page]);

        return $result['data']['currentUser']['tournaments']['nodes'] ?? [];
    }

    /**
     * List events within a tournament.
     */
    public function getTournamentEvents(string $slug): array
    {
        $result = $this->query('
            query TournamentEvents($slug: String!) {
                tournament(slug: $slug) {
                    events {
                        id
                        name
                        numEntrants
                    }
                }
            }
        ', ['slug' => $slug]);

        return $result['data']['tournament']['events'] ?? [];
    }

    /**
     * List sets (matches) within an event, with entrant names and scores.
     */
    public function getEventSets(int $eventId, int $page = 1): array
    {
        $result = $this->query('
            query EventSets($eventId: ID!, $page: Int!) {
                event(id: $eventId) {
                    sets(page: $page, perPage: 25, sortType: STANDARD) {
                        nodes {
                            id
                            fullRoundText
                            state
                            slots {
                                entrant {
                                    id
                                    name
                                }
                                standing {
                                    stats {
                                        score {
                                            value
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        ', ['eventId' => $eventId, 'page' => $page]);

        return $result['data']['event']['sets']['nodes'] ?? [];
    }

    /**
     * Get a single set with full slot/entrant details.
     */
    public function getSet(int $setId): array
    {
        $result = $this->query('
            query GetSet($setId: ID!) {
                set(id: $setId) {
                    id
                    fullRoundText
                    state
                    totalGames
                    slots {
                        entrant {
                            id
                            name
                        }
                        standing {
                            stats {
                                score {
                                    value
                                }
                            }
                        }
                    }
                }
            }
        ', ['setId' => $setId]);

        return $result['data']['set'] ?? [];
    }

    /**
     * Report the result of a bracket set.
     */
    public function reportSet(int $setId, int $winnerId, array $gameData = []): array
    {
        $result = $this->query('
            mutation ReportSet($setId: ID!, $winnerId: ID!, $gameData: [BracketSetGameDataInput]) {
                reportBracketSet(setId: $setId, winnerId: $winnerId, gameData: $gameData) {
                    id
                    state
                }
            }
        ', [
            'setId' => $setId,
            'winnerId' => $winnerId,
            'gameData' => $gameData,
        ]);

        return $result['data']['reportBracketSet'] ?? [];
    }

    /**
     * Execute a GraphQL query against the start.gg API.
     */
    private function query(string $gql, array $variables = []): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->post(self::API_URL, [
            'query' => $gql,
            'variables' => $variables,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('start.gg API returned HTTP '.$response->status());
        }

        $json = $response->json();

        if (! empty($json['errors'])) {
            $message = $json['errors'][0]['message'] ?? 'Unknown GraphQL error';

            throw new RuntimeException('start.gg GraphQL error: '.$message);
        }

        return $json;
    }
}
