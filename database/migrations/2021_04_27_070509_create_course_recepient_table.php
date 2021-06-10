<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseRecepientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_recepient', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('term_id');
            $table->unsignedBigInteger('article_id');
            $table->timestamps();

            $table->foreign('term_id')
                ->references('id')
                ->on('medical_terms')
                ->onDelete('cascade');

            $table->foreign('article_id')
                ->references('id')
                ->on('medical_articles')
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
        Schema::dropIfExists('course_recepient');
    }
}
