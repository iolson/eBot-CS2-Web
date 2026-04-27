<?php

use App\Models\Season;
use App\Models\Team;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('Season factory', function () {
    it('creates a season with factory', function () {
        $season = Season::factory()->create();
        expect($season->exists)->toBeTrue()
            ->and($season->name)->toContain('Season');
    });

    it('creates an active season with active() state', function () {
        $season = Season::factory()->active()->create();
        expect($season->active)->toBeTrue();
    });
});

describe('Season relationships', function () {
    it('belongs to many teams via teams_in_seasons', function () {
        $season = Season::factory()->create();
        $teams  = Team::factory(3)->create();
        $season->teams()->attach($teams->pluck('id'));

        expect($season->teams)->toHaveCount(3)
            ->and($season->teams->first())->toBeInstanceOf(Team::class);
    });
});
