<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublishingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'publishing' ) ) {
            Schema::create('publishing', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->text('name');
                $table->unsignedBigInteger('item_id');
                $table->timestamps();
            });

            Schema::table('publishing', function (Blueprint $table) {
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publishing');
    }
}
