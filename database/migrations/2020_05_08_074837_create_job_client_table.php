<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'job_client' ) ) {
            Schema::create('job_client', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('profile_id');
                $table->unsignedBigInteger('job_profession_id');
                $table->unsignedBigInteger('job_description_id');
                $table->timestamps();

                $table->foreign('job_profession_id')
                    ->references('id')
                    ->on('job_profession')
                    ->onDelete('cascade');

                $table->foreign('job_description_id')
                    ->references('id')
                    ->on('job_description')
                    ->onDelete('cascade');

                $table->foreign('profile_id')
                    ->references('id')
                    ->on('profiles')
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
        Schema::dropIfExists('job_client');
    }
}
