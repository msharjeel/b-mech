<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('service_title',500);
            $table->integer('service_id');
            $table->text('service_cat_id');
            $table->text('service_description');
            $table->float('min_cost');
            $table->float('max_cost')->nullable();
            $table->integer('service_duration');
            //$table->string('service_location', 500);
            //$table->double('latitude');
            //$table->double('longitude');
            $table->text('images');
            $table->text('vendor_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
}
