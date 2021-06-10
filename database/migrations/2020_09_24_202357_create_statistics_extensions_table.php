<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatisticsExtensionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistics_extensions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('statistics_field_id');
            $table->string('table', 100);
            $table->string('field', 100);
            $table->boolean('isMultiLang');
            $table->timestamps();
        });

        //Relationships
        Schema::table('statistics_extensions', function (Blueprint $table) {
            $table->foreign('statistics_field_id')->references('id')->on('statistics_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statistics_extensions');
    }
}
