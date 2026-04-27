<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id');
            $table->string('map_name', 50)->nullable();
            $table->bigInteger('score_1')->nullable();
            $table->bigInteger('score_2')->nullable();
            // Stored as VARCHAR to match Doctrine enum behavior
            $table->string('current_side')->nullable();
            $table->smallInteger('status')->nullable();
            $table->string('maps_for')->nullable();
            $table->bigInteger('nb_ot')->nullable();
            $table->bigInteger('identifier_id')->nullable();
            $table->string('tv_record_file', 255)->nullable();
            $table->timestamps();

            $table->index('match_id');
            $table->foreign('match_id')->references('id')->on('matchs')->cascadeOnDelete();
        });

        // Resolve circular FK: matchs.current_map → maps.id
        // Both tables now exist, safe to add the constraint.
        Schema::table('matchs', function (Blueprint $table) {
            $table->foreign('current_map')->references('id')->on('maps')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('matchs', function (Blueprint $table) {
            $table->dropForeign(['current_map']);
        });
        Schema::dropIfExists('maps');
    }
};
