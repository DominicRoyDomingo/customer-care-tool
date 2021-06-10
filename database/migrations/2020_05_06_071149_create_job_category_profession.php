<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobCategoryProfession extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'job_category_profession' ) ) {
            Schema::create('job_category_profession', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('job_category_id');
                $table->unsignedBigInteger('job_profession_id');
                $table->timestamps();

                $table->foreign('job_category_id')
                    ->references('id')
                    ->on('job_category')
                    ->onDelete('cascade');

                $table->foreign('job_profession_id')
                    ->references('id')
                    ->on('job_profession')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_category_profession');
    }
}
