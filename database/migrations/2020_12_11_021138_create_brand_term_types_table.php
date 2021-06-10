<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandTermTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brand_term_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('term_type_id');
            $table->bigInteger('brand_id');
            $table->timestamps();

            $table->foreign('term_type_id')
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
        Schema::dropIfExists('brand_term_types');
    }
}
