<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesExclusiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services_exclusive', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_type');
            $table->text('text_display');
            $table->timestamps();
        });

        Schema::table('services_exclusive', function (Blueprint $table) {
            $table->foreign('service_type')->references('id')->on('service_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services_exclusive');
    }
}
