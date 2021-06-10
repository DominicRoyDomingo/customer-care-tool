<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_category', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('brand');
            $table->unsignedBigInteger('category');
            $table->timestamps();
        });

        Schema::table('brand_category', function (Blueprint $table) {
            $table->foreign('brand')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('category')->references('id')->on('job_category')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brand_category');
    }
}
