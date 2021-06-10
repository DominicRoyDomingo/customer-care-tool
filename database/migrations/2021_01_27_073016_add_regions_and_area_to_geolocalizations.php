<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegionsAndAreaToGeolocalizations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('geolocalizations', function (Blueprint $table) {
            $table->unsignedBigInteger('regions')->nullable();

            $table->foreign('regions')
                ->references('id')
                ->on('world_regions')
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
        Schema::table('geolocalizations', function (Blueprint $table) {
            //
        });
    }
}
