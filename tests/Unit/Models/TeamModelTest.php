<?php

use App\Models\Team;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

describe('Team model', function () {
    it('uses the teams table', function () {
        expect((new Team)->getTable())->toBe('teams');
    });
});

describe('Team factory', function () {
    it('creates a team with factory', function () {
        $team = Team::factory()->make();

        expect($team->name)->toBeString()
            ->and($team->shorthandle)->toBeString();
    });
});

describe('Team relationships', function () {
    it('has many matches as team A', function () {
        expect((new Team)->matchesAsTeamA())
            ->toBeInstanceOf(HasMany::class);
    });

    it('has many matches as team B', function () {
        expect((new Team)->matchesAsTeamB())
            ->toBeInstanceOf(HasMany::class);
    });

    it('belongs to many seasons', function () {
        expect((new Team)->seasons())
            ->toBeInstanceOf(BelongsToMany::class);
    });
});
