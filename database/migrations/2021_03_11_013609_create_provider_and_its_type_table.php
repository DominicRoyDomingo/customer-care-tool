<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProviderAndItsTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_and_its_type', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('provider');
            $table->unsignedBigInteger('type_of_provider');
            $table->timestamps();

            $table->foreign('type_of_provider')
                ->references('id')
                ->on('medical_terms')
                ->onDelete('cascade');

            $table->foreign('provider')
                ->references('id')
                ->on('providers')
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
        Schema::dropIfExists('provider_and_its_type');
    }
}
