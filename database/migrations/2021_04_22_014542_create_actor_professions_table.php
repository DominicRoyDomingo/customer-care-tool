<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActorProfessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actor_professions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('actor');
            $table->unsignedBigInteger('profession');
            $table->timestamps();
        });

        Schema::table('actor_professions', function (Blueprint $table) {
            $table->foreign('actor')->references('id')->on('actors')->onDelete('cascade');
            $table->foreign('profession')->references('id')->on('medical_terms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actor_professions');
    }
}
