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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('service_id');
            $table->integer('service_request_id');
            $table->integer('user_id');
            $table->double('order_amount');
            $table->string('paid_status',250);
            $table->string('payment_through',250);
            $table->string('status',500);
            $table->string('vat_percentage',50);
            $table->double('vat_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
