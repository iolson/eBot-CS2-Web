<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('round', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id');
            $table->unsignedBigInteger('map_id');
            $table->string('event_name')->nullable();
            $table->text('event_text')->nullable();
            $table->bigInteger('event_time')->nullable();
            $table->unsignedBigInteger('kill_id')->nullable();
            $table->bigInteger('round_id')->nullable();
            $table->timestamps();

            $table->index('match_id');
            $table->index('map_id');
            $table->index('kill_id');
            $table->foreign('match_id')->references('id')->on('matchs')->cascadeOnDelete();
            $table->foreign('map_id')->references('id')->on('maps')->cascadeOnDelete();
            $table->foreign('kill_id')->references('id')->on('player_kill')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('round');
    }
};
