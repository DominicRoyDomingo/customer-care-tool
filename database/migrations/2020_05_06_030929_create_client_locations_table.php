<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'client_locations' ) ) {
            Schema::create('client_locations', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('profile_id');
                $table->unsignedBigInteger('world_cities_id')->nullable();
                $table->unsignedBigInteger('world_provinces_id')->nullable();
                $table->unsignedBigInteger('world_countries_id')->nullable();
                $table->timestamps();
            });

            //Relationships
            Schema::table('client_locations', function (Blueprint $table) {
                $table->foreign('profile_id')->references('id')->on('profiles');
                $table->foreign('world_cities_id')->references('id')->on('world_cities');
                $table->foreign('world_provinces_id')->references('id')->on('world_divisions');
                $table->foreign('world_countries_id')->references('id')->on('world_countries');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_locations');
    }
}
