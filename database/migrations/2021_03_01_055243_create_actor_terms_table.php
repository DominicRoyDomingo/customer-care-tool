<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActorTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actor_terms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('actor');
            $table->unsignedBigInteger('term');
            $table->timestamps();

            $table->foreign('term')
                ->references('id')
                ->on('medical_terms')
                ->onDelete('cascade');

            $table->foreign('actor')
                ->references('id')
                ->on('actors')
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
        Schema::dropIfExists('actor_terms');
    }
}
