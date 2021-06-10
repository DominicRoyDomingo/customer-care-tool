<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeOfDataToInformationType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('information_type', function (Blueprint $table) {
            $table->enum('type_of_data', array('Long Text', 'Short Text', 'Date', 'Email Format', 'Number'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('information_type', function (Blueprint $table) {
            //
        });
    }
}
