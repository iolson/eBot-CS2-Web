<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sf_guard_user', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email_address')->unique();
            $table->string('username', 128)->unique();
            $table->string('algorithm', 128)->default('sha1');
            $table->string('salt', 128)->nullable();
            $table->string('password', 128)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_super_admin')->default(false);
            $table->dateTime('last_login')->nullable();
            $table->timestamps();

            $table->index('is_active');
        });

        Schema::create('sf_guard_group', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('sf_guard_permission', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('sf_guard_user_group', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('group_id');
            $table->timestamps();

            $table->primary(['user_id', 'group_id']);
            $table->foreign('user_id')->references('id')->on('sf_guard_user')->cascadeOnDelete();
            $table->foreign('group_id')->references('id')->on('sf_guard_group')->cascadeOnDelete();
        });

        Schema::create('sf_guard_user_permission', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();

            $table->primary(['user_id', 'permission_id']);
            $table->foreign('user_id')->references('id')->on('sf_guard_user')->cascadeOnDelete();
            $table->foreign('permission_id')->references('id')->on('sf_guard_permission')->cascadeOnDelete();
        });

        Schema::create('sf_guard_group_permission', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();

            $table->primary(['group_id', 'permission_id']);
            $table->foreign('group_id')->references('id')->on('sf_guard_group')->cascadeOnDelete();
            $table->foreign('permission_id')->references('id')->on('sf_guard_permission')->cascadeOnDelete();
        });

        Schema::create('sf_guard_remember_key', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('remember_key', 32)->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('sf_guard_user')->cascadeOnDelete();
        });

        Schema::create('sf_guard_forgot_password', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('unique_key')->nullable();
            $table->dateTime('expires_at');
            $table->timestamps();

            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('sf_guard_user')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sf_guard_forgot_password');
        Schema::dropIfExists('sf_guard_remember_key');
        Schema::dropIfExists('sf_guard_group_permission');
        Schema::dropIfExists('sf_guard_user_permission');
        Schema::dropIfExists('sf_guard_user_group');
        Schema::dropIfExists('sf_guard_permission');
        Schema::dropIfExists('sf_guard_group');
        Schema::dropIfExists('sf_guard_user');
    }
};
