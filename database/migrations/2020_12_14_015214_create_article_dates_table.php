<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_dates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigIncrements('article_id');
            $table->date('article_date');
            $table->bigIncrements('type_date_id');
            $table->timestamps();

            $table->foreign('article_id')
                ->references('id')
                ->on('medical_articles')
                ->onDelete('cascade');

            $table->foreign('type_date_id')
                ->references('id')
                ->on('type_dates')
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
        Schema::dropIfExists('article_dates');
    }
}
