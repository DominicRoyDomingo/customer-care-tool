<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesBrandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services_brand', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service');
            $table->unsignedBigInteger('brands');
            $table->timestamps();
        });

        Schema::table('services_brand', function (Blueprint $table) {
            $table->foreign('service')->references('id')->on('services')->onDelete('cascade');
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
        Schema::dropIfExists('services_brand');
    }
}
