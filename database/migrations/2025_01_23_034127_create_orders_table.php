<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('provider_id')->constrained()->onDelete('cascade');
            $table->boolean('washing')->default(false);
            $table->boolean('ironing')->default(false);
            $table->boolean('dry_cleaning')->default(false);
            $table->integer('extra_load_small')->default(0); // For 1-10 extra pieces
            $table->integer('extra_load_large')->default(0); // For >10 extra pieces
            $table->decimal('total', 10, 2);
            $table->string('status')->default('pending');
            $table->string('address')->nullable();
            $table->decimal('latitude', 11, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->dateTime('pickup_time')->nullable();
            $table->dateTime('delivery_time')->nullable();
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
