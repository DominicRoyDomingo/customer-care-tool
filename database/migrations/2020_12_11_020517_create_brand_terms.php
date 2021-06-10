<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandTerms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brand_terms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('terminology_id');
            $table->bigInteger('brand_id');
            $table->timestamps();


            $table->foreign('terminology_id')
                ->references('id')
                ->on('brand')
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
        Schema::dropIfExists('brand_terms');
    }
}
