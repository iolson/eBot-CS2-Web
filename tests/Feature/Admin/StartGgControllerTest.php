<?php

use App\Models\Matchs;
use App\Models\User;
use App\Services\StartGgService;
use Illuminate\Support\Facades\Http;

describe('start.gg admin controller', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
    });

    it('shows unconfigured notice when token is missing', function () {
        app()->instance(StartGgService::class, new StartGgService(''));

        $this->actingAs($this->admin)
            ->get(route('admin.startgg.index'))
            ->assertOk()
            ->assertSee('not configured');
    });

    it('lists tournaments from the API', function () {
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

        app()->instance(StartGgService::class, new StartGgService('tok'));

        $this->actingAs($this->admin)
            ->get(route('admin.startgg.index'))
            ->assertOk()
            ->assertSee('Test Cup');
    });

    it('shows events when tournament is selected', function () {
        Http::fake([
            'api.start.gg/*' => Http::sequence()
                ->push([
                    'data' => [
                        'currentUser' => [
                            'tournaments' => ['nodes' => [['id' => 1, 'name' => 'Cup', 'slug' => 'cup']]],
                        ],
                    ],
                ], 200)
                ->push([
                    'data' => [
                        'tournament' => [
                            'events' => [
                                ['id' => 100, 'name' => 'CS2 Open', 'numEntrants' => 16],
                            ],
                        ],
                    ],
                ], 200),
        ]);

        app()->instance(StartGgService::class, new StartGgService('tok'));

        $this->actingAs($this->admin)
            ->get(route('admin.startgg.index', ['tournament' => 'cup']))
            ->assertOk()
            ->assertSee('CS2 Open');
    });

    it('shows api error gracefully', function () {
        Http::fake([
            'api.start.gg/*' => Http::response('Forbidden', 403),
        ]);

        app()->instance(StartGgService::class, new StartGgService('tok'));

        $this->actingAs($this->admin)
            ->get(route('admin.startgg.index'))
            ->assertOk()
            ->assertSee('API Error');
    });

    it('imports a set from start.gg', function () {
        Http::fake([
            'api.start.gg/*' => Http::response([
                'data' => [
                    'set' => [
                        'id' => 500,
                        'state' => 1,
                        'slots' => [
                            ['entrant' => ['id' => 10, 'name' => 'Team Alpha']],
                            ['entrant' => ['id' => 20, 'name' => 'Team Beta']],
                        ],
                    ],
                ],
            ], 200),
        ]);

        app()->instance(StartGgService::class, new StartGgService('tok'));

        $this->actingAs($this->admin)
            ->postJson(route('admin.startgg.import'), [
                'eventId' => 100,
                'setId' => 500,
            ])
            ->assertOk()
            ->assertJson(['status' => true]);

        $this->assertDatabaseHas('matchs', [
            'team_a_name' => 'Team Alpha',
            'team_b_name' => 'Team Beta',
            'identifier_id' => 'startgg.100.500',
        ]);
    });

    it('returns existing match when already imported', function () {
        $match = Matchs::factory()->create(['identifier_id' => 'startgg.100.500']);

        Http::fake([
            'api.start.gg/*' => Http::response([
                'data' => [
                    'set' => [
                        'id' => 500,
                        'slots' => [
                            ['entrant' => ['id' => 10, 'name' => 'Team Alpha']],
                            ['entrant' => ['id' => 20, 'name' => 'Team Beta']],
                        ],
                    ],
                ],
            ], 200),
        ]);

        app()->instance(StartGgService::class, new StartGgService('tok'));

        $this->actingAs($this->admin)
            ->postJson(route('admin.startgg.import'), [
                'eventId' => 100,
                'setId' => 500,
            ])
            ->assertOk()
            ->assertJson(['status' => false, 'matchId' => $match->id]);
    });

    it('returns error when not configured on import', function () {
        app()->instance(StartGgService::class, new StartGgService(''));

        $this->actingAs($this->admin)
            ->postJson(route('admin.startgg.import'), [
                'eventId' => 100,
                'setId' => 500,
            ])
            ->assertOk()
            ->assertJson(['status' => false, 'error' => 'not_configured']);
    });

    it('returns error when params are missing on import', function () {
        app()->instance(StartGgService::class, new StartGgService('tok'));

        $this->actingAs($this->admin)
            ->postJson(route('admin.startgg.import'), [])
            ->assertOk()
            ->assertJson(['status' => false, 'error' => 'missing_params']);
    });

    it('exports match results to start.gg', function () {
        Http::fake([
            'api.start.gg/*' => Http::sequence()
                ->push([
                    'data' => [
                        'set' => [
                            'id' => 500,
                            'slots' => [
                                ['entrant' => ['id' => 10, 'name' => 'Team Alpha']],
                                ['entrant' => ['id' => 20, 'name' => 'Team Beta']],
                            ],
                        ],
                    ],
                ], 200)
                ->push([
                    'data' => [
                        'reportBracketSet' => ['id' => 500, 'state' => 3],
                    ],
                ], 200),
        ]);

        app()->instance(StartGgService::class, new StartGgService('tok'));

        $match = Matchs::factory()->finished()->create([
            'identifier_id' => 'startgg.100.500',
            'score_a' => 2,
            'score_b' => 1,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.startgg.export', $match))
            ->assertRedirect()
            ->assertSessionHas('success');
    });

    it('rejects export for match without start.gg identifier', function () {
        app()->instance(StartGgService::class, new StartGgService('tok'));

        $match = Matchs::factory()->finished()->create(['identifier_id' => null]);

        $this->actingAs($this->admin)
            ->post(route('admin.startgg.export', $match))
            ->assertRedirect()
            ->assertSessionHas('error');
    });

    it('rejects export for match with non-startgg identifier', function () {
        app()->instance(StartGgService::class, new StartGgService('tok'));

        $match = Matchs::factory()->finished()->create(['identifier_id' => 't1.m1.1']);

        $this->actingAs($this->admin)
            ->post(route('admin.startgg.export', $match))
            ->assertRedirect()
            ->assertSessionHas('error');
    });

    it('rejects export when match has no winner', function () {
        Http::fake([
            'api.start.gg/*' => Http::response([
                'data' => [
                    'set' => [
                        'id' => 500,
                        'slots' => [
                            ['entrant' => ['id' => 10, 'name' => 'Team Alpha']],
                            ['entrant' => ['id' => 20, 'name' => 'Team Beta']],
                        ],
                    ],
                ],
            ], 200),
        ]);

        app()->instance(StartGgService::class, new StartGgService('tok'));

        $match = Matchs::factory()->create([
            'identifier_id' => 'startgg.100.500',
            'status' => Matchs::STATUS_NOT_STARTED,
            'score_a' => 0,
            'score_b' => 0,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.startgg.export', $match))
            ->assertRedirect()
            ->assertSessionHas('error');
    });
});
