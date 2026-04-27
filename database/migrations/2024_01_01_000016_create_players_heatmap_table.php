<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players_heatmap', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id');
            $table->unsignedBigInteger('map_id');
            $table->string('event_name', 50)->nullable();
            $table->double('event_x')->nullable();
            $table->double('event_y')->nullable();
            $table->double('event_z')->nullable();
            $table->string('player_name')->nullable();
            $table->unsignedBigInteger('player_id')->nullable();
            $table->string('player_team', 20)->nullable();
            $table->double('attacker_x')->nullable();
            $table->double('attacker_y')->nullable();
            $table->double('attacker_z')->nullable();
            $table->string('attacker_name')->nullable();
            $table->unsignedBigInteger('attacker_id')->nullable();
            $table->string('attacker_team', 20)->nullable();
            $table->bigInteger('round_id')->nullable();
            $table->bigInteger('round_time')->nullable();
            $table->timestamps();

            $table->index('match_id');
            $table->index('map_id');
            $table->index('player_id');
            $table->index('attacker_id');
            $table->foreign('match_id')->references('id')->on('matchs')->cascadeOnDelete();
            $table->foreign('map_id')->references('id')->on('maps')->cascadeOnDelete();
            $table->foreign('player_id')->references('id')->on('players')->cascadeOnDelete();
            $table->foreign('attacker_id')->references('id')->on('players')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players_heatmap');
    }
};
