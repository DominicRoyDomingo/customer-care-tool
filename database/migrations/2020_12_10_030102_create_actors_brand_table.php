<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActorsBrandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actors_brand', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('actor');
            $table->unsignedBigInteger('brands');
            $table->timestamps();
        });

        Schema::table('actors_brand', function (Blueprint $table) {
            $table->foreign('actor')->references('id')->on('actors')->onDelete('cascade');
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
        Schema::dropIfExists('actors_brand');
    }
}
