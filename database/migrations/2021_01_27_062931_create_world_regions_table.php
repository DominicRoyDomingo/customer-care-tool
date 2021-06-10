<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorldRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('world_regions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('country');
            $table->text('region');
            $table->timestamps();

            $table->foreign('country')
                ->references('id')
                ->on('world_countries')
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
        Schema::dropIfExists('world_regions');
    }
}
