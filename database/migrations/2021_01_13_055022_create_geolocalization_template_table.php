<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeolocalizationTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geolocalization_template', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('topic');
            $table->text('page_title')->nullable();
            $table->text('body')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('slug')->nullable();
            $table->text('alt_tag_image')->nullable();
            $table->text('img_description')->nullable();
            $table->timestamps();
        });

        Schema::table('geolocalization_template', function (Blueprint $table) {
            $table->foreign('topic')->references('id')->on('medical_articles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geolocalization_template');
    }
}
