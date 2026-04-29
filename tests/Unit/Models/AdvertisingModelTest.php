<?php

use App\Models\Advertising;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

describe('Advertising model', function () {
    it('uses the advertising table', function () {
        expect((new Advertising)->getTable())->toBe('advertising');
    });

    it('casts active to boolean', function () {
        $ad = new Advertising(['active' => 1]);

        expect($ad->active)->toBeTrue();
    });
});

describe('Advertising factory', function () {
    it('creates an active ad by default', function () {
        $ad = Advertising::factory()->make();

        expect($ad->active)->toBeTrue()
            ->and($ad->message)->toBeString();
    });

    it('creates an inactive ad with inactive() state', function () {
        $ad = Advertising::factory()->inactive()->make();

        expect($ad->active)->toBeFalse();
    });
});

describe('Advertising scopeActive()', function () {
    it('adds a where active=true constraint', function () {
        $sql = Advertising::active()->toSql();

        expect($sql)->toContain('"active"');
    });
});

describe('Advertising relationships', function () {
    it('belongs to an event', function () {
        expect((new Advertising)->event())
            ->toBeInstanceOf(BelongsTo::class);
    });
});
