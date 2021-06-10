<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'profiles' ) ) {
            Schema::create('profiles', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('primary_email')->nullable();
                $table->string('surname');
                $table->string('firstname');
                $table->string('middlename')->nullable();
                $table->string('nickname')->nullable();
                $table->text('notes');
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
        Schema::dropIfExists('profiles');
    }
}
