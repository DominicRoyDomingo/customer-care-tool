<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandOrganizationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'brand_organization' ) ) {
            Schema::create('brand_organization', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('brand');
                $table->unsignedBigInteger('organization');
                $table->timestamps();
            });

            Schema::table('brand_organization', function (Blueprint $table) {
                $table->foreign('brand')->references('id')->on('brands')->onDelete('cascade');
                $table->foreign('organization')->references('id')->on('organizations')->onDelete('cascade');
                // $table->foreign('tag')->references('id')->on('tags')->onDelete('cascade');
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
        Schema::dropIfExists('brand_organization');
    }
}
