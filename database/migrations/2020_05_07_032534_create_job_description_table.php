<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'job_description' ) ) {
            Schema::create('job_description', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('description');
                $table->unsignedBigInteger('job_profession_id');
                $table->timestamps();

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
        Schema::dropIfExists('job_description');
    }
}
