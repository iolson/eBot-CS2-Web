<?php

use App\Models\Event;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Event factory', function () {
    it('creates an event with factory', function () {
        $event = Event::factory()->create();
        expect($event->exists)->toBeTrue()
            ->and($event->name)->toBeString();
    });

    it('creates an active event with active() state', function () {
        $event = Event::factory()->active()->create();
        expect($event->active)->toBeTrue();
    });
});

describe('Event relationships', function () {
    it('belongs to many teams via teams_in_events', function () {
        $event = Event::factory()->create();
        $teams = Team::factory(3)->create();
        $event->teams()->attach($teams->pluck('id'));

        expect($event->teams)->toHaveCount(3)
            ->and($event->teams->first())->toBeInstanceOf(Team::class);
    });
});
