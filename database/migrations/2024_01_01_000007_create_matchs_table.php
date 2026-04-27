<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Note: current_map FK (→ maps.id) is added in the maps migration to resolve
        // the circular dependency: matchs.current_map → maps and maps.match_id → matchs.
        Schema::create('matchs', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 50)->nullable();
            $table->unsignedBigInteger('server_id')->nullable();
            $table->unsignedBigInteger('season_id')->nullable();
            $table->unsignedBigInteger('team_a')->nullable();
            $table->string('team_a_flag', 2)->nullable();
            $table->string('team_a_name', 25)->nullable();
            $table->unsignedBigInteger('team_b')->nullable();
            $table->string('team_b_flag', 2)->nullable();
            $table->string('team_b_name', 25)->nullable();
            $table->smallInteger('status')->nullable();
            $table->boolean('is_paused')->nullable();
            $table->bigInteger('score_a')->nullable();
            $table->bigInteger('score_b')->nullable();
            $table->integer('max_round');
            $table->string('rules', 200)->nullable();
            $table->bigInteger('overtime_startmoney')->nullable();
            $table->integer('overtime_max_round')->nullable();
            $table->boolean('config_full_score')->nullable();
            $table->boolean('config_ot')->nullable();
            $table->boolean('config_streamer')->nullable();
            $table->boolean('config_knife_round')->nullable();
            $table->boolean('config_switch_auto')->nullable();
            $table->boolean('config_auto_change_password')->nullable();
            $table->string('config_password', 50)->nullable();
            $table->boolean('config_heatmap')->nullable();
            $table->string('config_authkey', 200)->nullable();
            $table->boolean('enable')->nullable();
            // Stored as VARCHAR to match Doctrine enum behavior
            $table->string('map_selection_mode')->nullable();
            $table->boolean('ingame_enable')->nullable();
            // current_map: FK added in maps migration
            $table->unsignedBigInteger('current_map')->nullable();
            $table->boolean('force_zoom_match')->nullable();
            // identifier_id: Toornament composite key "<tournamentId>.<matchId>.<gameNumber>"
            $table->string('identifier_id', 100)->nullable();
            $table->dateTime('startdate')->nullable();
            $table->boolean('auto_start')->nullable();
            $table->integer('auto_start_time')->nullable();
            $table->timestamps();

            $table->index('server_id');
            $table->index('team_a');
            $table->index('team_b');
            $table->index('current_map');
            $table->index('season_id');

            $table->foreign('server_id')->references('id')->on('servers')->nullOnDelete();
            $table->foreign('team_a')->references('id')->on('teams')->nullOnDelete();
            $table->foreign('team_b')->references('id')->on('teams')->nullOnDelete();
            $table->foreign('season_id')->references('id')->on('seasons')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchs');
    }
};
