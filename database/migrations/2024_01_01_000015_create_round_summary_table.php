<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('round_summary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id');
            $table->unsignedBigInteger('map_id');
            $table->boolean('bomb_planted')->nullable();
            $table->boolean('bomb_defused')->nullable();
            $table->boolean('bomb_exploded')->nullable();
            $table->string('win_type')->default('normal'); // bombdefused, bombeexploded, normal, saved
            $table->string('team_win')->nullable();        // 'a' or 'b'
            $table->boolean('ct_win')->nullable();
            $table->boolean('t_win')->nullable();
            $table->smallInteger('score_a')->nullable();
            $table->smallInteger('score_b')->nullable();
            $table->unsignedBigInteger('best_killer')->nullable();
            $table->bigInteger('best_killer_nb')->nullable();
            $table->boolean('best_killer_fk')->nullable();
            $table->text('best_action_type')->nullable();
            $table->text('best_action_param')->nullable();
            $table->string('backup_file_name')->nullable();
            $table->bigInteger('round_id')->nullable();
            $table->timestamps();

            $table->index('match_id');
            $table->index('map_id');
            $table->index('best_killer');
            $table->foreign('match_id')->references('id')->on('matchs')->cascadeOnDelete();
            $table->foreign('map_id')->references('id')->on('maps')->cascadeOnDelete();
            $table->foreign('best_killer')->references('id')->on('players')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('round_summary');
    }
};
