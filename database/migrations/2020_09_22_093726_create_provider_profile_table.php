<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProviderProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_profile', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('provider');
            $table->unsignedBigInteger('profile');
            $table->timestamps();
        });

        Schema::table('provider_profile', function (Blueprint $table) {
            $table->foreign('provider')->references('id')->on('providers')->onDelete('cascade');
            $table->foreign('profile')->references('id')->on('profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provider_profile');
    }
}
