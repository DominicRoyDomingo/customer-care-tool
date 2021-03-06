<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupIdToProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id')->nullable();
        });

        Schema::table('providers', function (Blueprint $table) {
            $table->foreign('group_id')->references('id')->on('provider_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('providers', function (Blueprint $table) {
            //
        });
    }
}
