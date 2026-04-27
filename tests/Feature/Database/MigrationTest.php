<?php

use Illuminate\Support\Facades\Schema;

describe('eBot database tables exist after migrations', function () {
    $tables = [
        'sf_guard_user',
        'sf_guard_group',
        'sf_guard_permission',
        'sf_guard_user_group',
        'sf_guard_user_permission',
        'sf_guard_group_permission',
        'sf_guard_remember_key',
        'sf_guard_forgot_password',
        'servers',
        'seasons',
        'teams',
        'configs',
        'teams_in_seasons',
        'matchs',
        'maps',
        'maps_score',
        'advertising',
        'players',
        'player_kill',
        'players_snapshot',
        'round',
        'round_summary',
        'players_heatmap',
    ];

    foreach ($tables as $table) {
        it("has {$table} table", function () use ($table) {
            expect(Schema::hasTable($table))->toBeTrue();
        });
    }
});

describe('matchs table columns', function () {
    it('has required columns', function () {
        $columns = [
            'id', 'ip', 'server_id', 'season_id', 'team_a', 'team_b',
            'team_a_flag', 'team_a_name', 'team_b_flag', 'team_b_name',
            'status', 'is_paused', 'score_a', 'score_b', 'max_round',
            'config_ot', 'config_knife_round', 'config_heatmap',
            'enable', 'current_map', 'identifier_id',
            'startdate', 'auto_start', 'auto_start_time',
            'created_at', 'updated_at',
        ];

        foreach ($columns as $column) {
            expect(Schema::hasColumn('matchs', $column))->toBeTrue(
                "Expected matchs to have column: {$column}"
            );
        }
    });
});

describe('maps table columns', function () {
    it('has required columns', function () {
        $columns = [
            'id', 'match_id', 'map_name', 'status',
            'score_1', 'score_2', 'current_side', 'maps_for',
            'nb_ot', 'tv_record_file', 'created_at', 'updated_at',
        ];

        foreach ($columns as $column) {
            expect(Schema::hasColumn('maps', $column))->toBeTrue(
                "Expected maps to have column: {$column}"
            );
        }
    });
});

describe('seasons table columns', function () {
    it('has required columns including logo and active', function () {
        expect(Schema::hasColumn('seasons', 'logo'))->toBeTrue()
            ->and(Schema::hasColumn('seasons', 'active'))->toBeTrue()
            ->and(Schema::hasColumn('seasons', 'name'))->toBeTrue()
            ->and(Schema::hasColumn('seasons', 'event'))->toBeTrue();
    });
});

describe('sf_guard_user table columns', function () {
    it('has password and algorithm columns for auth', function () {
        expect(Schema::hasColumn('sf_guard_user', 'username'))->toBeTrue()
            ->and(Schema::hasColumn('sf_guard_user', 'password'))->toBeTrue()
            ->and(Schema::hasColumn('sf_guard_user', 'algorithm'))->toBeTrue()
            ->and(Schema::hasColumn('sf_guard_user', 'salt'))->toBeTrue()
            ->and(Schema::hasColumn('sf_guard_user', 'is_active'))->toBeTrue()
            ->and(Schema::hasColumn('sf_guard_user', 'is_super_admin'))->toBeTrue();
    });
});

describe('players table columns', function () {
    it('has kill/death stat columns', function () {
        $statColumns = ['nb_kill', 'assist', 'death', 'hs', 'defuse', 'bombe', 'tk'];

        foreach ($statColumns as $column) {
            expect(Schema::hasColumn('players', $column))->toBeTrue(
                "Expected players to have column: {$column}"
            );
        }
    });
});

describe('players_heatmap table columns', function () {
    it('has xyz coordinate columns for event and attacker', function () {
        expect(Schema::hasColumn('players_heatmap', 'event_x'))->toBeTrue()
            ->and(Schema::hasColumn('players_heatmap', 'event_y'))->toBeTrue()
            ->and(Schema::hasColumn('players_heatmap', 'event_z'))->toBeTrue()
            ->and(Schema::hasColumn('players_heatmap', 'attacker_x'))->toBeTrue()
            ->and(Schema::hasColumn('players_heatmap', 'attacker_y'))->toBeTrue()
            ->and(Schema::hasColumn('players_heatmap', 'attacker_z'))->toBeTrue();
    });
});
