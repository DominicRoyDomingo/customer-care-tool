<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'content' ) ) {
            Schema::create('content', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->text('name')->nullable();
                $table->unsignedBigInteger('status');
                $table->text('clipart')->nullable();
                $table->timestamps();
            });

            Schema::table('content', function (Blueprint $table) {
                $table->foreign('status')->references('id')->on('date_statuses')->onDelete('cascade');;
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
        Schema::dropIfExists('content');
    }
}
