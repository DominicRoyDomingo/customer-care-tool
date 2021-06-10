<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTermIconTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('term_icon', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('term');
            $table->unsignedBigInteger('provider_type');
            $table->text('icon');
            $table->timestamps();

            $table->foreign('term')
                ->references('id')
                ->on('medical_terms')
                ->onDelete('cascade');

            $table->foreign('provider_type')
                ->references('id')
                ->on('medical_terms')
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
        Schema::dropIfExists('term_icon');
    }
}
