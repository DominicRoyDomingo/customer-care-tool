<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParameterToProviderServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provider_services', function (Blueprint $table) {
            $table->unsignedBigInteger('parameter')->nullable();
        });

        Schema::table('provider_services', function (Blueprint $table) {
            $table->foreign('parameter')->references('id')->on('parameter')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('provider_services', function (Blueprint $table) {
            //
        });
    }
}
