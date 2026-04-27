<?php

use App\Models\GameMap;
use App\Models\Matchs;
use App\Models\Season;

describe('Public frontend routes', function () {
    it('shows homepage', function () {
        $this->get(route('home'))->assertOk();
    });

    it('shows match index', function () {
        $this->get(route('matchs.index'))->assertOk();
    });

    it('shows archived matches', function () {
        $this->get(route('matchs.archived'))->assertOk();
    });

    it('shows match detail', function () {
        $match = Matchs::factory()->create();

        $this->get(route('matchs.show', $match))->assertOk();
    });

    it('shows stats index', function () {
        $this->get(route('stats.index'))->assertOk();
    });

    it('shows global stats', function () {
        $this->get(route('stats.global'))->assertOk();
    });

    it('shows seasons index', function () {
        $this->get(route('seasons.index'))->assertOk();
    });

    it('shows stream view', function () {
        $match = Matchs::factory()->create();

        $this->get(route('stream.show', $match))->assertOk();
    });

    it('shows widget match players', function () {
        $match = Matchs::factory()->create();

        $this->get(route('widget.match-players', $match))->assertOk();
    });

    it('shows widget live stats', function () {
        $this->get(route('widget.live-stats'))->assertOk();
    });
});

describe('Match export endpoints', function () {
    it('exports players as JSON', function () {
        $match = Matchs::factory()->create();

        $this->getJson(route('matchs.export.players', $match))
            ->assertOk()
            ->assertJsonStructure(['match_id', 'players']);
    });

    it('exports rounds as JSON', function () {
        $match = Matchs::factory()->create();

        $this->getJson(route('matchs.export.rounds', $match))
            ->assertOk()
            ->assertJsonStructure(['match_id', 'rounds']);
    });

    it('exports kills as JSON', function () {
        $match = Matchs::factory()->create();

        $this->getJson(route('matchs.export.kills', $match))
            ->assertOk()
            ->assertJsonStructure(['match_id', 'kills']);
    });

    it('exports full estats as JSON', function () {
        $match = Matchs::factory()->create();

        $this->getJson(route('matchs.export.estats', $match))
            ->assertOk()
            ->assertJsonStructure(['match', 'maps']);
    });

    it('returns JSON heatmap data', function () {
        $match = Matchs::factory()->create();

        $this->postJson(route('matchs.heatmap-data', $match), ['type' => 'kill'])
            ->assertOk()
            ->assertJsonIsArray();
    });
});

describe('Season select', function () {
    it('selects a season and redirects to match list', function () {
        $season = Season::factory()->create();

        $this->get(route('seasons.select', $season))
            ->assertRedirect(route('matchs.index'));

        $this->assertEquals($season->id, session('selected_season_id'));
    });

    it('redirects to archived matches when site=archived', function () {
        $season = Season::factory()->create();

        $this->get(route('seasons.select', [$season, 'site' => 'archived']))
            ->assertRedirect(route('matchs.archived'));
    });
});

describe('Stats routes', function () {
    it('shows map statistics', function () {
        $this->get(route('stats.maps'))->assertOk();
    });

    it('shows weapon statistics', function () {
        $this->get(route('stats.weapons'))->assertOk();
    });

    it('shows entry kills statistics', function () {
        $this->get(route('stats.entry-kills'))->assertOk();
    });

    it('returns 404 for unknown player', function () {
        $this->get(route('stats.player', 'STEAM_0:0:99999999'))
            ->assertNotFound();
    });

    it('shows gun round statistics', function () {
        $this->get(route('stats.gunround'))->assertOk();
    });
});

describe('Match logs', function () {
    it('returns 404 when log file does not exist', function () {
        $match = Matchs::factory()->create();

        $this->get(route('matchs.logs', $match))->assertNotFound();
    });
});

describe('Demo downloads', function () {
    it('returns 403 when demo downloads are disabled', function () {
        config(['ebot.demo_download' => false]);

        $map = GameMap::factory()->create(['tv_record_file' => 'demo_123']);

        $this->get(route('matchs.demo', $map))->assertForbidden();
    });

    it('returns 404 when demo file does not exist on disk', function () {
        config(['ebot.demo_download' => true]);
        config(['ebot.demo_path' => '/nonexistent/path']);

        $map = GameMap::factory()->create(['tv_record_file' => 'demo_missing']);

        $this->get(route('matchs.demo', $map))->assertNotFound();
    });

    it('returns 404 when tv_record_file is not set', function () {
        config(['ebot.demo_download' => true]);

        $map = GameMap::factory()->create(['tv_record_file' => null]);

        $this->get(route('matchs.demo', $map))->assertNotFound();
    });

    it('returns 404 when tv_record_file contains path traversal', function () {
        config(['ebot.demo_download' => true]);

        $map = GameMap::factory()->create(['tv_record_file' => '../../../etc/passwd']);

        $this->get(route('matchs.demo', $map))->assertNotFound();
    });
});
