<?php

use App\Services\StartGgService;
use Illuminate\Support\Facades\Http;

describe('StartGgService', function () {
    function makeStartGgService(): StartGgService
    {
        return new StartGgService('test-token');
    }

    it('reports not configured when token is empty', function () {
        $service = new StartGgService('');
        expect($service->isConfigured())->toBeFalse();
    });

    it('reports configured when token is present', function () {
        $service = makeStartGgService();
        expect($service->isConfigured())->toBeTrue();
    });

    it('sends correct authorization header', function () {
        Http::fake([
            'api.start.gg/*' => Http::response([
                'data' => ['currentUser' => ['tournaments' => ['nodes' => []]]],
            ], 200),
        ]);

        $service = makeStartGgService();
        $service->getTournamentsByOwner();

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer test-token')
                && str_contains($request->url(), 'api.start.gg/gql/alpha');
        });
    });

    it('sends GraphQL query in request body', function () {
        Http::fake([
            'api.start.gg/*' => Http::response([
                'data' => ['currentUser' => ['tournaments' => ['nodes' => []]]],
            ], 200),
        ]);

        $service = makeStartGgService();
        $service->getTournamentsByOwner();

        Http::assertSent(function ($request) {
            $body = $request->data();

            return isset($body['query']) && isset($body['variables']);
        });
    });

    it('fetches tournaments by owner', function () {
        Http::fake([
            'api.start.gg/*' => Http::response([
                'data' => [
                    'currentUser' => [
                        'tournaments' => [
                            'nodes' => [
                                ['id' => 1, 'name' => 'Test Cup', 'slug' => 'test-cup'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = makeStartGgService();
        $tournaments = $service->getTournamentsByOwner();

        expect($tournaments)->toHaveCount(1);
        expect($tournaments[0]['name'])->toBe('Test Cup');
    });

    it('fetches tournament events', function () {
        Http::fake([
            'api.start.gg/*' => Http::response([
                'data' => [
                    'tournament' => [
                        'events' => [
                            ['id' => 100, 'name' => 'CS2 Open', 'numEntrants' => 16],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = makeStartGgService();
        $events = $service->getTournamentEvents('test-cup');

        expect($events)->toHaveCount(1);
        expect($events[0]['name'])->toBe('CS2 Open');
    });

    it('fetches event sets', function () {
        Http::fake([
            'api.start.gg/*' => Http::response([
                'data' => [
                    'event' => [
                        'sets' => [
                            'nodes' => [
                                [
                                    'id' => 500,
                                    'fullRoundText' => 'Grand Final',
                                    'state' => 1,
                                    'slots' => [
                                        ['entrant' => ['id' => 10, 'name' => 'Team A']],
                                        ['entrant' => ['id' => 20, 'name' => 'Team B']],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = makeStartGgService();
        $sets = $service->getEventSets(100);

        expect($sets)->toHaveCount(1);
        expect($sets[0]['fullRoundText'])->toBe('Grand Final');
    });

    it('fetches a single set', function () {
        Http::fake([
            'api.start.gg/*' => Http::response([
                'data' => [
                    'set' => [
                        'id' => 500,
                        'state' => 3,
                        'slots' => [
                            ['entrant' => ['id' => 10, 'name' => 'Team Alpha']],
                            ['entrant' => ['id' => 20, 'name' => 'Team Beta']],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = makeStartGgService();
        $set = $service->getSet(500);

        expect($set['id'])->toBe(500);
        expect($set['slots'])->toHaveCount(2);
    });

    it('reports a set result', function () {
        Http::fake([
            'api.start.gg/*' => Http::response([
                'data' => [
                    'reportBracketSet' => [
                        'id' => 500,
                        'state' => 3,
                    ],
                ],
            ], 200),
        ]);

        $service = makeStartGgService();
        $result = $service->reportSet(500, 10, [['gameNum' => 1, 'winnerId' => 10]]);

        expect($result['id'])->toBe(500);
        expect($result['state'])->toBe(3);
    });

    it('throws on HTTP error response', function () {
        Http::fake([
            'api.start.gg/*' => Http::response('Unauthorized', 401),
        ]);

        $service = makeStartGgService();
        expect(fn () => $service->getTournamentsByOwner())->toThrow(RuntimeException::class);
    });

    it('throws on GraphQL error response', function () {
        Http::fake([
            'api.start.gg/*' => Http::response([
                'errors' => [['message' => 'Invalid token']],
            ], 200),
        ]);

        $service = makeStartGgService();
        expect(fn () => $service->getTournamentsByOwner())->toThrow(
            RuntimeException::class,
            'start.gg GraphQL error: Invalid token',
        );
    });
});
