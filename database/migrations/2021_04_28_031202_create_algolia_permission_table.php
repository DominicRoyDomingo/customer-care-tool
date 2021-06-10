<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlgoliaPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('algolia_permission', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('class');
            $table->unsignedBigInteger('brand');
            $table->boolean('isAllowed');
            $table->text('index_name');
            $table->timestamps();

            $table->foreign('class')
                ->references('id')
                ->on('classes')
                ->onDelete('cascade');

            $table->foreign('brand')
                ->references('id')
                ->on('brands')
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
        Schema::dropIfExists('algolia_permission');
    }
}
