<?php

use App\Models\Matchs;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Player constants', function () {
    it('has team constants', function () {
        expect(Player::TEAM_A)->toBe('a')
            ->and(Player::TEAM_B)->toBe('b')
            ->and(Player::TEAM_OTHER)->toBe('other');
    });

    it('has side constants', function () {
        expect(Player::SIDE_CT)->toBe('ct')
            ->and(Player::SIDE_T)->toBe('t')
            ->and(Player::SIDE_OTHER)->toBe('other');
    });
});

describe('Player getKdRatio()', function () {
    it('divides kills by deaths', function () {
        $player = new Player(['nb_kill' => 20, 'death' => 10]);
        expect($player->getKdRatio())->toBe(2.0);
    });

    it('returns kills as float when deaths is zero', function () {
        $player = new Player(['nb_kill' => 15, 'death' => 0]);
        expect($player->getKdRatio())->toBe(15.0);
    });

    it('rounds to 2 decimal places', function () {
        $player = new Player(['nb_kill' => 10, 'death' => 3]);
        expect($player->getKdRatio())->toBe(3.33);
    });
});

describe('Player getHsPercent()', function () {
    it('calculates headshot percentage', function () {
        $player = new Player(['nb_kill' => 10, 'hs' => 5]);
        expect($player->getHsPercent())->toBe(50.0);
    });

    it('returns 0 when no kills', function () {
        $player = new Player(['nb_kill' => 0, 'hs' => 0]);
        expect($player->getHsPercent())->toBe(0.0);
    });

    it('rounds to 1 decimal place', function () {
        $player = new Player(['nb_kill' => 3, 'hs' => 1]);
        expect($player->getHsPercent())->toBe(33.3);
    });
});

describe('Player scopes', function () {
    it('teamA() scope filters to team a', function () {
        $match = Matchs::factory()->create();
        Player::factory()->teamA()->create(['match_id' => $match->id]);
        Player::factory()->teamB()->create(['match_id' => $match->id]);

        expect(Player::teamA()->count())->toBe(1);
    });

    it('teamB() scope filters to team b', function () {
        $match = Matchs::factory()->create();
        Player::factory()->teamA()->create(['match_id' => $match->id]);
        Player::factory()->teamB()->create(['match_id' => $match->id]);

        expect(Player::teamB()->count())->toBe(1);
    });
});

describe('Player factory', function () {
    it('creates a player with factory', function () {
        $player = Player::factory()->create();
        expect($player->exists)->toBeTrue()
            ->and($player->team)->toBeIn([Player::TEAM_A, Player::TEAM_B]);
    });
});
