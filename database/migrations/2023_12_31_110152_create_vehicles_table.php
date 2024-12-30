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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('label', 75);
            $table->string('vehicle_country', 75);
            $table->string('vehicle_make', 75);
            $table->string('vehicle_model', 75);
            $table->string('vehicle_year', 10);
            $table->string('vehicle_transmission', 10);
            $table->string('vehicle_drive', 10);
            $table->string('vehicle_displacement', 10);
            $table->string('vehicle_cylinder', 10);
            $table->string('vehicle_class', 10);
            $table->integer('user_id');
            $table->char('active',1)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
