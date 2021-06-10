<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatisticsFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistics_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->text('display');
            $table->unsignedBigInteger('statistics_table_id');
            $table->boolean('isForeign');
            $table->timestamps();
        });

        //Relationships
        Schema::table('statistics_fields', function (Blueprint $table) {
            $table->foreign('statistics_table_id')->references('id')->on('statistics_tables');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statistics_fields');
    }
}
