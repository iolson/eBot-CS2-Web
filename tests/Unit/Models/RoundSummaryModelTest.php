<?php

use App\Models\RoundSummary;

describe('RoundSummary win type constants', function () {
    it('has win type constants', function () {
        expect(RoundSummary::WIN_TYPE_BOMB_DEFUSED)->toBe('bombdefused')
            ->and(RoundSummary::WIN_TYPE_BOMB_EXPLODED)->toBe('bombeexploded')
            ->and(RoundSummary::WIN_TYPE_NORMAL)->toBe('normal')
            ->and(RoundSummary::WIN_TYPE_SAVED)->toBe('saved');
    });
});
