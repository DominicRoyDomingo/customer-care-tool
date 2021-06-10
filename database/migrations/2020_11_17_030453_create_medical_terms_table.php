<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medical_terms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name');
            $table->text('term_types');
            $table->text('specializations');
            $table->text('notes');
            $table->text('img_url');
            $table->text('icon_url');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medical_terms');
    }
}
