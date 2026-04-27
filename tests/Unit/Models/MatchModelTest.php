<?php

use App\Models\Matchs;
use App\Models\Season;
use App\Models\Server;
use App\Models\Team;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('Matchs status constants', function () {
    it('has all 15 status constants defined', function () {
        expect(Matchs::STATUS_NOT_STARTED)->toBe(0)
            ->and(Matchs::STATUS_STARTING)->toBe(1)
            ->and(Matchs::STATUS_WU_KNIFE)->toBe(2)
            ->and(Matchs::STATUS_KNIFE)->toBe(3)
            ->and(Matchs::STATUS_END_KNIFE)->toBe(4)
            ->and(Matchs::STATUS_WU_1_SIDE)->toBe(5)
            ->and(Matchs::STATUS_FIRST_SIDE)->toBe(6)
            ->and(Matchs::STATUS_WU_2_SIDE)->toBe(7)
            ->and(Matchs::STATUS_SECOND_SIDE)->toBe(8)
            ->and(Matchs::STATUS_WU_OT_1_SIDE)->toBe(9)
            ->and(Matchs::STATUS_OT_FIRST_SIDE)->toBe(10)
            ->and(Matchs::STATUS_WU_OT_2_SIDE)->toBe(11)
            ->and(Matchs::STATUS_OT_SECOND_SIDE)->toBe(12)
            ->and(Matchs::STATUS_END_MATCH)->toBe(13)
            ->and(Matchs::STATUS_ARCHIVE)->toBe(14);
    });

    it('has map selection mode constants', function () {
        expect(Matchs::MAP_SELECTION_BO2)->toBe('bo2')
            ->and(Matchs::MAP_SELECTION_BO3_MODEA)->toBe('bo3_modea')
            ->and(Matchs::MAP_SELECTION_BO3_MODEB)->toBe('bo3_modeb')
            ->and(Matchs::MAP_SELECTION_NORMAL)->toBe('normal');
    });
});

describe('Matchs table configuration', function () {
    it('uses the matchs table', function () {
        expect((new Matchs())->getTable())->toBe('matchs');
    });
});

describe('Matchs getStatusText()', function () {
    it('returns correct text for each status', function () {
        $texts = [
            Matchs::STATUS_NOT_STARTED    => 'Not started',
            Matchs::STATUS_STARTING       => 'Starting',
            Matchs::STATUS_WU_KNIFE       => 'Warmup Knife',
            Matchs::STATUS_KNIFE          => 'Knife Round',
            Matchs::STATUS_END_KNIFE      => 'Waiting choose team',
            Matchs::STATUS_WU_1_SIDE      => 'Warmup first side',
            Matchs::STATUS_FIRST_SIDE     => 'First side',
            Matchs::STATUS_WU_2_SIDE      => 'Warmup second side',
            Matchs::STATUS_SECOND_SIDE    => 'Second side',
            Matchs::STATUS_WU_OT_1_SIDE   => 'Warmup first side OT',
            Matchs::STATUS_OT_FIRST_SIDE  => 'First side OT',
            Matchs::STATUS_WU_OT_2_SIDE   => 'Warmup second side OT',
            Matchs::STATUS_OT_SECOND_SIDE => 'Second side OT',
            Matchs::STATUS_END_MATCH      => 'Finished',
            Matchs::STATUS_ARCHIVE        => 'Archived',
        ];

        foreach ($texts as $status => $expected) {
            $model = new Matchs(['status' => $status]);
            expect($model->getStatusText())->toBe($expected);
        }
    });

    it('returns Unknown for invalid status', function () {
        $model = new Matchs(['status' => 99]);
        expect($model->getStatusText())->toBe('Unknown');
    });
});

describe('Matchs isLive()', function () {
    it('returns true when enabled and status is in-progress', function () {
        $model = new Matchs(['enable' => true, 'status' => Matchs::STATUS_FIRST_SIDE]);
        expect($model->isLive())->toBeTrue();
    });

    it('returns false when status is not started', function () {
        $model = new Matchs(['enable' => true, 'status' => Matchs::STATUS_NOT_STARTED]);
        expect($model->isLive())->toBeFalse();
    });

    it('returns false when status is end match', function () {
        $model = new Matchs(['enable' => true, 'status' => Matchs::STATUS_END_MATCH]);
        expect($model->isLive())->toBeFalse();
    });

    it('returns false when disabled', function () {
        $model = new Matchs(['enable' => false, 'status' => Matchs::STATUS_FIRST_SIDE]);
        expect($model->isLive())->toBeFalse();
    });
});

describe('Matchs isArchived()', function () {
    it('returns true when status is archive', function () {
        $model = new Matchs(['status' => Matchs::STATUS_ARCHIVE]);
        expect($model->isArchived())->toBeTrue();
    });

    it('returns false for non-archive status', function () {
        $model = new Matchs(['status' => Matchs::STATUS_END_MATCH]);
        expect($model->isArchived())->toBeFalse();
    });
});

describe('Matchs getNbRound()', function () {
    it('sums scores and adds 1', function () {
        $model = new Matchs(['score_a' => 10, 'score_b' => 5]);
        expect($model->getNbRound())->toBe(16);
    });

    it('returns 1 when scores are zero', function () {
        $model = new Matchs(['score_a' => 0, 'score_b' => 0]);
        expect($model->getNbRound())->toBe(1);
    });
});

describe('Matchs factory', function () {
    it('creates a match with factory', function () {
        $match = Matchs::factory()->create();
        expect($match->exists)->toBeTrue()
            ->and($match->status)->toBe(Matchs::STATUS_NOT_STARTED);
    });

    it('creates a live match with live() state', function () {
        $match = Matchs::factory()->live()->create();
        expect($match->status)->toBe(Matchs::STATUS_FIRST_SIDE)
            ->and($match->enable)->toBeTrue();
    });

    it('creates a finished match with finished() state', function () {
        $match = Matchs::factory()->finished()->create();
        expect($match->status)->toBe(Matchs::STATUS_END_MATCH);
    });

    it('creates an archived match with archived() state', function () {
        $match = Matchs::factory()->archived()->create();
        expect($match->status)->toBe(Matchs::STATUS_ARCHIVE);
    });
});

describe('Matchs scopes', function () {
    it('live() scope returns only in-progress enabled matches', function () {
        Matchs::factory()->live()->create();
        Matchs::factory()->notStarted()->create();
        Matchs::factory()->finished()->create();

        expect(Matchs::live()->count())->toBe(1);
    });

    it('notStarted() scope returns status 0 matches', function () {
        Matchs::factory()->notStarted()->create();
        Matchs::factory()->live()->create();

        expect(Matchs::notStarted()->count())->toBe(1);
    });

    it('finished() scope returns status 13 matches', function () {
        Matchs::factory()->finished()->create();
        Matchs::factory()->archived()->create();

        expect(Matchs::finished()->count())->toBe(1);
    });

    it('archived() scope returns status 14 matches', function () {
        Matchs::factory()->archived()->create();
        Matchs::factory()->finished()->create();

        expect(Matchs::archived()->count())->toBe(1);
    });

    it('active() scope excludes archived matches', function () {
        Matchs::factory()->notStarted()->create();
        Matchs::factory()->finished()->create();
        Matchs::factory()->archived()->create();

        expect(Matchs::active()->count())->toBe(2);
    });
});

describe('Matchs relationships', function () {
    it('belongs to a server', function () {
        $match = Matchs::factory()->create();
        expect($match->server)->toBeInstanceOf(Server::class);
    });

    it('belongs to a season', function () {
        $match = Matchs::factory()->create();
        expect($match->season)->toBeInstanceOf(Season::class);
    });

    it('belongs to team a', function () {
        $match = Matchs::factory()->create();
        expect($match->teamA)->toBeInstanceOf(Team::class);
    });

    it('belongs to team b', function () {
        $match = Matchs::factory()->create();
        expect($match->teamB)->toBeInstanceOf(Team::class);
    });
});
