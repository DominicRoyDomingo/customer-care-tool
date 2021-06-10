<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'client_jobs' ) ) {
            Schema::create('client_jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('profile_id');
                $table->unsignedBigInteger('job_category_id');
                $table->unsignedBigInteger('job_profession_id')->nullable();
                $table->unsignedBigInteger('job_description_id')->nullable();
                $table->timestamps();
            });

            //Relationships
            Schema::table('client_jobs', function (Blueprint $table) {
                
                $table->foreign('job_category_id')
                    ->references('id')
                    ->on('job_category')
                    ->onDelete('cascade');
                
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
        Schema::dropIfExists('client_jobs');
    }
}
