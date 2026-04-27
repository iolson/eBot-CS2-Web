<?php

use App\Models\Matchs;
use App\Models\User;
use App\Services\ToornamentService;
use Illuminate\Support\Facades\Http;

describe('Toornament admin controller', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
    });

    it('shows unconfigured notice when API key is missing', function () {
        // Bind an unconfigured service
        app()->instance(ToornamentService::class, new ToornamentService('', '', ''));

        $this->actingAs($this->admin)
            ->get(route('admin.toornament.index'))
            ->assertOk()
            ->assertSee('not configured');
    });

    it('lists tournaments from the API', function () {
        Http::fake([
            '*oauth*' => Http::response(json_encode(['access_token' => 'tok']), 200),
            '*v1/tournaments' => Http::response(json_encode([
                ['id' => 't1', 'name' => 'Test Cup'],
            ]), 200),
        ]);

        app()->instance(ToornamentService::class, new ToornamentService('id', 'secret', 'key'));

        $this->actingAs($this->admin)
            ->get(route('admin.toornament.index'))
            ->assertOk()
            ->assertSee('Test Cup');
    });

    it('shows api error gracefully', function () {
        Http::fake([
            '*oauth*' => Http::response(json_encode(['access_token' => 'tok']), 200),
            '*v1/tournaments' => Http::response('Forbidden', 403),
        ]);

        app()->instance(ToornamentService::class, new ToornamentService('id', 'secret', 'key'));

        $this->actingAs($this->admin)
            ->get(route('admin.toornament.index'))
            ->assertOk()
            ->assertSee('API Error');
    });

    it('imports a match from Toornament', function () {
        Http::fake([
            '*oauth*' => Http::response(json_encode(['access_token' => 'tok']), 200),
            '*tournaments/t1/matches*' => Http::response(json_encode([
                'id' => 'm1',
                'stage_number' => '1',
                'opponents' => [
                    ['participant' => ['name' => 'Team Alpha', 'country' => 'US']],
                    ['participant' => ['name' => 'Team Beta',  'country' => 'DE']],
                ],
                'games' => [['map' => 'de_dust2']],
            ]), 200),
            '*tournaments/t1/stages*' => Http::response(json_encode([
                'type' => 'group',
            ]), 200),
        ]);

        app()->instance(ToornamentService::class, new ToornamentService('id', 'secret', 'key'));

        $this->actingAs($this->admin)
            ->postJson(route('admin.toornament.import'), [
                'toornamentId' => 't1',
                'toornamentMatchId' => 'm1',
                'gameId' => 1,
            ])
            ->assertOk()
            ->assertJson(['status' => true]);

        $this->assertDatabaseHas('matchs', [
            'team_a_name' => 'Team Alpha',
            'team_b_name' => 'Team Beta',
            'identifier_id' => 't1.m1.1',
        ]);
    });

    it('returns existing match when already imported', function () {
        $match = Matchs::factory()->create(['identifier_id' => 't1.m1.1']);

        Http::fake([
            '*oauth*' => Http::response(json_encode(['access_token' => 'tok']), 200),
            '*tournaments/t1/matches*' => Http::response(json_encode([
                'id' => 'm1',
                'stage_number' => '1',
                'opponents' => [
                    ['participant' => ['name' => 'Team Alpha', 'country' => 'US']],
                    ['participant' => ['name' => 'Team Beta',  'country' => 'DE']],
                ],
                'games' => [['map' => 'de_dust2']],
            ]), 200),
            '*tournaments/t1/stages*' => Http::response(json_encode(['type' => 'single_elimination']), 200),
        ]);

        app()->instance(ToornamentService::class, new ToornamentService('id', 'secret', 'key'));

        $this->actingAs($this->admin)
            ->postJson(route('admin.toornament.import'), [
                'toornamentId' => 't1',
                'toornamentMatchId' => 'm1',
                'gameId' => 1,
            ])
            ->assertOk()
            ->assertJson(['status' => false, 'matchId' => $match->id]);
    });

    it('returns error when not configured on import', function () {
        app()->instance(ToornamentService::class, new ToornamentService('', '', ''));

        $this->actingAs($this->admin)
            ->postJson(route('admin.toornament.import'), [
                'toornamentId' => 't1',
                'toornamentMatchId' => 'm1',
            ])
            ->assertOk()
            ->assertJson(['status' => false, 'error' => 'not_configured']);
    });

    it('exports match results to Toornament', function () {
        Http::fake([
            '*oauth*' => Http::response(json_encode(['access_token' => 'tok']), 200),
            '*games/1/result*' => Http::sequence()
                ->push(['opponents' => [[], []], 'status' => 'pending'], 200)
                ->push([], 200),
            '*games/1' => Http::response([], 200),
        ]);

        app()->instance(ToornamentService::class, new ToornamentService('id', 'secret', 'key'));

        $match = Matchs::factory()->finished()->create(['identifier_id' => 't1.m1.1']);

        $this->actingAs($this->admin)
            ->post(route('admin.toornament.export', $match))
            ->assertRedirect()
            ->assertSessionHas('success');
    });

    it('rejects export for match without identifier', function () {
        app()->instance(ToornamentService::class, new ToornamentService('id', 'secret', 'key'));

        $match = Matchs::factory()->finished()->create(['identifier_id' => null]);

        $this->actingAs($this->admin)
            ->post(route('admin.toornament.export', $match))
            ->assertRedirect()
            ->assertSessionHas('error');
    });
});
