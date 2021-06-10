<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'contacts' ) ) {
            Schema::create('contacts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('contact_type_id');
                $table->text('contact_info');
                $table->unsignedBigInteger('created_by');
                $table->unsignedBigInteger('profile_id');
                $table->timestamps();
            });
        

            //Relationships
            Schema::table('contacts', function (Blueprint $table) {
                $table->foreign('profile_id')->references('id')->on('profiles');
                $table->foreign('contact_type_id')->references('id')->on('contact_types');
                $table->foreign('created_by')->references('id')->on('users');
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
        Schema::dropIfExists('contacts');
    }
}
