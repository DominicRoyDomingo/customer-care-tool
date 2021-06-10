<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableReasons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'reasons' ) ) {
            Schema::create('reasons', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->text('name')->nullable();
                $table->text('description')->nullable();
                $table->text('brands')->nullable();
                $table->integer('org_id')->nullable();
                $table->unsignedBigInteger('request_type');
                $table->timestamps();
            });

            //Relationships
            Schema::table('reasons', function (Blueprint $table) {
                $table->foreign('request_type')->references('id')->on('request_type')->onDelete('cascade');
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
        Schema::dropIfExists('table_reasons');
    }
}
