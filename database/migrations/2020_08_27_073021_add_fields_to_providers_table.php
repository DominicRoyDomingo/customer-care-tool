<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->unsignedBigInteger('country')->nullable();
            $table->unsignedBigInteger('city')->nullable();
            $table->unsignedBigInteger('division')->nullable();
        });

        Schema::table('providers', function (Blueprint $table) {
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
        Schema::table('providerss', function (Blueprint $table) {
            //
        });
    }
}
