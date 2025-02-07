<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::table('providers', function (Blueprint $table) {
        $table->string('service')->nullable()->after('address'); // laundry, gardener, or cleaning
        $table->json('categories')->nullable()->after('service'); // Store categories as JSON array
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    Schema::table('providers', function (Blueprint $table) {
        $table->dropColumn(['service', 'categories']);
    });
    }
};
