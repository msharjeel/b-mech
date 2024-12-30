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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name',500);
            $table->double('amount');
            $table->timestamp('expiry')->nullable();
            $table->double('min_spend')->nullable();
            $table->double('max_spend')->nullable();
            $table->text('exculde_vendor')->nullable();
            $table->text('exculde_user')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
