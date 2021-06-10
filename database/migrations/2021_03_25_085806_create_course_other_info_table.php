<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseOtherInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_other_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('information_type_id');
            $table->unsignedBigInteger('article_id');
            $table->timestamps();

            $table->foreign('information_type_id')
                ->references('id')
                ->on('information_type')
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
        Schema::dropIfExists('course_other_info');
    }
}
