<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveServiceColumnsFromOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'washing',
                'ironing',
                'dry_cleaning',
                'extra_load_small',
                'extra_load_large'
            ]);
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('washing')->default(false);
            $table->boolean('ironing')->default(false);
            $table->boolean('dry_cleaning')->default(false);
            $table->boolean('extra_load_small')->default(false);
            $table->boolean('extra_load_large')->default(false);
        });
    }
}