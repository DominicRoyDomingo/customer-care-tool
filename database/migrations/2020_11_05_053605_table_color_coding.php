<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableColorCoding extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'color_coding' ) ) {
            Schema::create('color_coding', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->text('color_name')->nullable();
                $table->string('hexcode')->nullable();
                $table->text('description')->nullable();
                $table->integer('slot_limit')->nullable();
                $table->text('brands')->nullable();;
                $table->timestamps();
            });

            //Relationships
            // Schema::table('reasons', function (Blueprint $table) {
            //     $table->foreign('request_type')->references('id')->on('request_type')->onDelete('cascade');
            // });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('color_coding');
    }
}
