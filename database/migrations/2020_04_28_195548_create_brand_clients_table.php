<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'brand_clients' ) ) {
            Schema::create('brand_clients', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('profile_id');
                $table->unsignedBigInteger('brand_id');
                $table->timestamps();
            });

            //Relationships
            Schema::table('brand_clients', function (Blueprint $table) {
                $table->foreign('profile_id')->references('id')->on('profiles');
                $table->foreign('brand_id')->references('id')->on('brands');
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
        Schema::dropIfExists('brand_clients');
    }
}
