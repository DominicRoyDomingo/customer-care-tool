<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProviderOtherInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_other_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('provider');
            $table->unsignedBigInteger('type_of_info');
            $table->text('value');
            $table->timestamps();
            $table->foreign('provider')
            ->references('id')
            ->on('providers')
            ->onDelete('cascade');
            $table->foreign('type_of_info')
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
        Schema::dropIfExists('provider_other_info');
    }
}
