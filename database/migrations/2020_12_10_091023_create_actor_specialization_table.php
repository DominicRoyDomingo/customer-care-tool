<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActorSpecializationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actor_specialization', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('actor');
            $table->bigInteger('category')->nullable();
            $table->bigInteger('profession')->nullable();
            $table->bigInteger('specialization')->nullable();
            $table->timestamps();
        });

        Schema::table('actor_specialization', function (Blueprint $table) {
            $table->foreign('actor')->references('id')->on('actors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actor_specialization');
    }
}
