<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players_snapshot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id');
            $table->string('player_key')->nullable();
            $table->string('first_side')->nullable();
            $table->string('current_side')->nullable();
            $table->bigInteger('nb_kill')->default(0);
            $table->bigInteger('assist')->default(0);
            $table->bigInteger('death')->default(0);
            $table->bigInteger('point')->default(0);
            $table->bigInteger('hs')->default(0);
            $table->bigInteger('defuse')->default(0);
            $table->bigInteger('bombe')->default(0);
            $table->bigInteger('tk')->default(0);
            $table->bigInteger('nb1')->default(0);
            $table->bigInteger('nb2')->default(0);
            $table->bigInteger('nb3')->default(0);
            $table->bigInteger('nb4')->default(0);
            $table->bigInteger('nb5')->default(0);
            $table->bigInteger('nb1kill')->default(0);
            $table->bigInteger('nb2kill')->default(0);
            $table->bigInteger('nb3kill')->default(0);
            $table->bigInteger('nb4kill')->default(0);
            $table->bigInteger('nb5kill')->default(0);
            $table->bigInteger('pluskill')->default(0);
            $table->bigInteger('firstkill')->default(0);
            $table->bigInteger('round_id')->nullable();
            $table->timestamps();

            $table->index('player_id');
            $table->foreign('player_id')->references('id')->on('players')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players_snapshot');
    }
};
