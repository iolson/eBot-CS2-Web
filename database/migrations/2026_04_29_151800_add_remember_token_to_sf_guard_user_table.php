<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sf_guard_user', function (Blueprint $table) {
            if (! Schema::hasColumn('sf_guard_user', 'remember_token')) {
                $table->rememberToken()->nullable()->after('password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sf_guard_user', function (Blueprint $table) {
            if (Schema::hasColumn('sf_guard_user', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }
};
