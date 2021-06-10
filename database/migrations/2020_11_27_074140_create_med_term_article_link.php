<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedTermArticleLink extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medical_term_article_link', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('medical_term_id')->nullable();
            $table->unsignedBigInteger('medical_article_id')->nullable();
            $table->timestamps();


            $table->foreign('medical_term_id')
                ->references('id')
                ->on('medical_terms')
                ->onDelete('cascade');

            $table->foreign('medical_article_id')
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
        Schema::dropIfExists('medical_term_article_link');
    }
}
