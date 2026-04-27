<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Models\Matchs;
use App\Models\User;

describe('Admin match controller', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
    });

    it('guests cannot access match index', function () {
        $this->get(route('admin.matchs.index'))->assertRedirect(route('login'));
    });

    it('non-admins are blocked', function () {
        $user = User::factory()->create(['is_super_admin' => false]);
        $this->actingAs($user)->get(route('admin.matchs.index'))->assertForbidden();
    });

    it('shows match index', function () {
        $this->actingAs($this->admin)
            ->get(route('admin.matchs.index'))
            ->assertOk();
    });

    it('shows archived matches', function () {
        $this->actingAs($this->admin)
            ->get(route('admin.matchs.archived'))
            ->assertOk();
    });

    it('shows create form', function () {
        $this->actingAs($this->admin)
            ->get(route('admin.matchs.create'))
            ->assertOk();
    });

    it('creates a match', function () {
        $teamA = \App\Models\Team::factory()->create();
        $teamB = \App\Models\Team::factory()->create();

        $this->actingAs($this->admin)
            ->post(route('admin.matchs.store'), [
                'team_a'             => $teamA->id,
                'team_b'             => $teamB->id,
                'max_round'          => 15,
                'map_selection_mode' => 1,
            ])
            ->assertRedirect(route('admin.matchs.index'));

        $this->assertDatabaseHas('matchs', ['team_a' => $teamA->id, 'team_b' => $teamB->id]);
    });

    it('rejects same team for team_a and team_b', function () {
        $team = \App\Models\Team::factory()->create();

        $this->actingAs($this->admin)
            ->post(route('admin.matchs.store'), [
                'team_a'             => $team->id,
                'team_b'             => $team->id,
                'max_round'          => 15,
                'map_selection_mode' => 1,
            ])
            ->assertSessionHasErrors('team_b');
    });

    it('shows match details', function () {
        $match = Matchs::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.matchs.show', $match))
            ->assertOk();
    });

    it('shows edit form for non-live match', function () {
        $match = Matchs::factory()->notStarted()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.matchs.edit', $match))
            ->assertOk();
    });

    it('redirects when editing a live match', function () {
        $match = Matchs::factory()->live()->create(['enable' => 1]);

        $this->actingAs($this->admin)
            ->get(route('admin.matchs.edit', $match))
            ->assertRedirect(route('admin.matchs.index'))
            ->assertSessionHas('error');
    });

    it('deletes a non-live non-archived match', function () {
        $match = Matchs::factory()->notStarted()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.matchs.destroy', $match))
            ->assertRedirect(route('admin.matchs.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($match);
    });

    it('blocks deleting a live match', function () {
        $match = Matchs::factory()->live()->create(['enable' => 1]);

        $this->actingAs($this->admin)
            ->delete(route('admin.matchs.destroy', $match))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertModelExists($match);
    });

    it('blocks deleting an archived match', function () {
        $match = Matchs::factory()->archived()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.matchs.destroy', $match))
            ->assertRedirect()
            ->assertSessionHas('error');
    });

    it('duplicates a match', function () {
        $match = Matchs::factory()->finished()->create();

        $this->actingAs($this->admin)
            ->post(route('admin.matchs.duplicate', $match))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseCount('matchs', 2);
    });

    it('archives a finished match', function () {
        $match = Matchs::factory()->finished()->create();

        $this->actingAs($this->admin)
            ->post(route('admin.matchs.archive', $match))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('matchs', ['id' => $match->id, 'status' => Matchs::STATUS_ARCHIVE]);
    });

    it('rejects archiving a non-finished match', function () {
        $match = Matchs::factory()->notStarted()->create();

        $this->actingAs($this->admin)
            ->post(route('admin.matchs.archive', $match))
            ->assertRedirect()
            ->assertSessionHas('error');
    });

    it('archives all finished matches', function () {
        Matchs::factory()->count(3)->finished()->create();
        Matchs::factory()->notStarted()->create();

        $this->actingAs($this->admin)
            ->post(route('admin.matchs.archive-all'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertEquals(3, Matchs::where('status', Matchs::STATUS_ARCHIVE)->count());
        $this->assertEquals(1, Matchs::where('status', Matchs::STATUS_NOT_STARTED)->count());
    });

    it('resets a disabled match', function () {
        $match = Matchs::factory()->create([
            'status'  => Matchs::STATUS_END_MATCH,
            'enable'  => 0,
            'score_a' => 10,
            'score_b' => 6,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.matchs.reset', $match))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('matchs', [
            'id'      => $match->id,
            'status'  => Matchs::STATUS_NOT_STARTED,
            'score_a' => 0,
            'score_b' => 0,
        ]);
    });

    it('blocks resetting a live match', function () {
        $match = Matchs::factory()->live()->create(['enable' => 1]);

        $this->actingAs($this->admin)
            ->post(route('admin.matchs.reset', $match))
            ->assertRedirect()
            ->assertSessionHas('error');
    });

    it('updates match score', function () {
        $match = Matchs::factory()->create();

        $this->actingAs($this->admin)
            ->patch(route('admin.matchs.edit-score', $match), [
                'score_a' => 14,
                'score_b' => 9,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('matchs', ['id' => $match->id, 'score_a' => 14, 'score_b' => 9]);
    });

    it('start returns info for not-started match', function () {
        $match = Matchs::factory()->notStarted()->create();

        $this->actingAs($this->admin)
            ->post(route('admin.matchs.start', $match))
            ->assertRedirect()
            ->assertSessionHas('info');
    });

    it('start rejects match not in not-started state', function () {
        $match = Matchs::factory()->live()->create(['enable' => 1]);

        $this->actingAs($this->admin)
            ->post(route('admin.matchs.start', $match))
            ->assertRedirect()
            ->assertSessionHas('error');
    });

    it('stop returns info for live match', function () {
        $match = Matchs::factory()->live()->create(['enable' => 1]);

        $this->actingAs($this->admin)
            ->post(route('admin.matchs.stop', $match))
            ->assertRedirect()
            ->assertSessionHas('info');
    });

    it('stop rejects non-live match', function () {
        $match = Matchs::factory()->notStarted()->create();

        $this->actingAs($this->admin)
            ->post(route('admin.matchs.stop', $match))
            ->assertRedirect()
            ->assertSessionHas('error');
    });
});
