<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'date_statuses' ) ) {
            Schema::create('date_statuses', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->text('status');
                $table->integer('sequence');
                $table->boolean('isLast');
                $table->enum('class', array('content', 'item', 'publishing'));
                $table->timestamps();
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
        Schema::dropIfExists('date_statuses');
    }
}
