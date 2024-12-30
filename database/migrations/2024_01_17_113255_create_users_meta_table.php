<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users_meta', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('service_id');
            $table->integer('country_id');
            $table->integer('vehicle_class_id');
            $table->json('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_meta');
    }
};
