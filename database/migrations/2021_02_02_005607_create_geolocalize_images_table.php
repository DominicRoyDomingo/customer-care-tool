<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeolocalizeImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geolocalize_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('place');
            $table->text('image');
            $table->timestamps();

            $table->foreign('place')
                ->references('id')
                ->on('geolocalizations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geolocalize_images');
    }
}
