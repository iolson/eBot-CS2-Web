<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id');
            $table->unsignedBigInteger('map_id')->nullable();
            $table->string('player_key')->nullable();
            $table->string('team')->default('other'); // 'a', 'b', 'other'
            $table->string('ip')->nullable();
            $table->string('steamid')->nullable();
            $table->string('first_side')->nullable();  // 'ct', 't', 'other'
            $table->string('current_side')->nullable(); // 'ct', 't', 'other'
            $table->string('pseudo')->nullable();
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
            $table->timestamps();

            $table->index('match_id');
            $table->index('map_id');
            $table->foreign('match_id')->references('id')->on('matchs')->cascadeOnDelete();
            $table->foreign('map_id')->references('id')->on('maps')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
