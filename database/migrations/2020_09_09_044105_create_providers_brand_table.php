<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvidersBrandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers_brand', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('provider');
            $table->unsignedBigInteger('brands');
            $table->timestamps();
        });

        Schema::table('providers_brand', function (Blueprint $table) {
            $table->foreign('provider')->references('id')->on('providers')->onDelete('cascade');
            $table->foreign('brands')->references('id')->on('brands')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('providers_brand');
    }
}
