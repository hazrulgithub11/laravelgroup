<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add telegram_username to users table
        if (!Schema::hasColumn('users', 'telegram_username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('telegram_username')->nullable()->after('email');
            });
        }

        // Add telegram_username to providers table
        if (!Schema::hasColumn('providers', 'telegram_username')) {
            Schema::table('providers', function (Blueprint $table) {
                $table->string('telegram_username')->nullable()->after('email');
            });
        }
    }

    public function down()
    {
        // Remove from users table
        if (Schema::hasColumn('users', 'telegram_username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('telegram_username');
            });
        }

        // Remove from providers table
        if (Schema::hasColumn('providers', 'telegram_username')) {
            Schema::table('providers', function (Blueprint $table) {
                $table->dropColumn('telegram_username');
            });
        }
    }
}; 