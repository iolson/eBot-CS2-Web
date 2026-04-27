<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Models\Season;
use App\Models\Team;
use App\Models\User;

describe('Admin team controller', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
    });

    it('shows team index', function () {
        $this->actingAs($this->admin)
            ->get(route('admin.teams.index'))
            ->assertOk();
    });

    it('creates a team', function () {
        $this->actingAs($this->admin)
            ->post(route('admin.teams.store'), [
                'name'        => 'Team Alpha',
                'shorthandle' => 'ALPH',
            ])
            ->assertRedirect(route('admin.teams.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('teams', ['name' => 'Team Alpha']);
    });

    it('creates a team and assigns seasons', function () {
        $season = Season::factory()->create();

        $this->actingAs($this->admin)
            ->post(route('admin.teams.store'), [
                'name'        => 'Team Bravo',
                'shorthandle' => 'BRV',
                'seasons'     => [$season->id],
            ])
            ->assertRedirect(route('admin.teams.index'));

        $team = Team::where('name', 'Team Bravo')->first();
        $this->assertTrue($team->seasons->contains($season));
    });

    it('updates a team', function () {
        $team = Team::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->admin)
            ->put(route('admin.teams.update', $team), [
                'name'        => 'New Name',
                'shorthandle' => 'NEW',
            ])
            ->assertRedirect(route('admin.teams.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => 'New Name']);
    });

    it('deletes a team', function () {
        $team = Team::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.teams.destroy', $team))
            ->assertRedirect(route('admin.teams.index'));

        $this->assertModelMissing($team);
    });

    it('returns teams in season as JSON', function () {
        $season = Season::factory()->create();
        $team   = Team::factory()->create();
        $team->seasons()->attach($season);

        $this->actingAs($this->admin)
            ->getJson(route('admin.teams.season-members', ['season_id' => $season->id]))
            ->assertOk()
            ->assertJsonFragment(['id' => $team->id, 'name' => $team->name]);
    });

    it('requires season_id for season-members endpoint', function () {
        $this->actingAs($this->admin)
            ->getJson(route('admin.teams.season-members'))
            ->assertUnprocessable();
    });
});
