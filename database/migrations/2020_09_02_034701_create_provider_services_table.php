<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProviderServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('providers');
            $table->unsignedBigInteger('services');
            $table->text('day_start');
            $table->text('day_end');
            $table->timestamps();
        });

        Schema::table('provider_services', function (Blueprint $table) {
            $table->foreign('providers')->references('id')->on('providers')->onDelete('cascade');
            $table->foreign('services')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provider_services');
    }
}
