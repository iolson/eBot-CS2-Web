<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Created before round because round.kill_id references player_kill.id
        Schema::create('player_kill', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id');
            $table->unsignedBigInteger('map_id');
            $table->string('killer_name', 100)->nullable();
            $table->unsignedBigInteger('killer_id')->nullable();
            $table->string('killer_team', 20)->nullable();
            $table->string('killed_name', 100)->nullable();
            $table->unsignedBigInteger('killed_id')->nullable();
            $table->string('killed_team', 20)->nullable();
            $table->string('weapon', 100)->nullable();
            $table->boolean('headshot')->nullable();
            $table->bigInteger('round_id')->nullable();
            $table->timestamps();

            $table->index('match_id');
            $table->index('map_id');
            $table->index('killer_id');
            $table->index('killed_id');
            $table->foreign('match_id')->references('id')->on('matchs')->cascadeOnDelete();
            $table->foreign('map_id')->references('id')->on('maps')->cascadeOnDelete();
            $table->foreign('killer_id')->references('id')->on('players')->cascadeOnDelete();
            $table->foreign('killed_id')->references('id')->on('players')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_kill');
    }
};
