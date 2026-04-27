<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maps_score', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('map_id');
            $table->string('type_score')->nullable(); // 'normal' or 'ot'
            $table->bigInteger('score1_side1')->nullable();
            $table->bigInteger('score1_side2')->nullable();
            $table->bigInteger('score2_side1')->nullable();
            $table->bigInteger('score2_side2')->nullable();
            $table->timestamps();

            $table->index('map_id');
            $table->foreign('map_id')->references('id')->on('maps')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maps_score');
    }
};
