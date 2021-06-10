<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'organizations' ) ) {
            Schema::create('organizations', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->text('name');
                $table->unsignedBigInteger('main_user');
                $table->text('location');
                $table->unsignedBigInteger('category');
                $table->text('other_info');
                $table->timestamps();
            });

            Schema::table('organizations', function (Blueprint $table) {
                $table->foreign('main_user')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('category')->references('id')->on('organization_category')->onDelete('cascade');
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
        Schema::dropIfExists('organizations');
    }
}
