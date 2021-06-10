<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'items' ) ) {
            Schema::create('items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->text('item_name');
                $table->unsignedBigInteger('content');
                $table->unsignedBigInteger('status');
                $table->unsignedBigInteger('item_type');
                $table->timestamps();
            });

            Schema::table('items', function (Blueprint $table) {
                $table->foreign('content')->references('id')->on('content')->onDelete('cascade');
                $table->foreign('status')->references('id')->on('date_statuses')->onDelete('cascade');
                $table->foreign('item_type')->references('id')->on('publishing_item_type')->onDelete('cascade');
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
        Schema::dropIfExists('items');
    }
}
