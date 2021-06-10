<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableQuestionLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('question');
            $table->integer('choice')->nullable()->default(0);
            $table->text('succeeding');
            $table->tinyInteger('iscorrect')->dafault(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_links');
    }
}
