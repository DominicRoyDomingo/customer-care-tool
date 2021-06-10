<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'organization_users' ) ) {
            Schema::create('organization_users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user');
                $table->unsignedBigInteger('organization');
                $table->timestamps();
            });

            Schema::table('organization_users', function (Blueprint $table) {
                $table->foreign('user')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('organization_users');
    }
}
