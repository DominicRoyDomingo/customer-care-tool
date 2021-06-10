<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeolocalizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geolocalizations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('article');
            $table->unsignedBigInteger('country')->nullable();
            $table->unsignedBigInteger('city')->nullable();
            $table->unsignedBigInteger('division')->nullable();
            $table->timestamps();
        });

        Schema::table('geolocalizations', function (Blueprint $table) {
            $table->foreign('article')->references('id')->on('medical_articles')->onDelete('cascade');
            $table->foreign('country')->references('id')->on('world_countries')->onDelete('cascade');
            $table->foreign('city')->references('id')->on('world_cities')->onDelete('cascade');
            $table->foreign('division')->references('id')->on('world_divisions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geolocalizations');
    }
}
