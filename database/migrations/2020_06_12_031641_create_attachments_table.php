<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'attachments' ) ) {
            Schema::create('attachments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('profile_id');
                $table->text('file_url')->nullable();
                $table->text('description')->nullable();
                $table->unsignedBigInteger('document_category_id')->nullable();
                $table->unsignedBigInteger('document_type_id')->nullable();
                $table->unsignedBigInteger('added_by');
                $table->text('tracker_notes')->nullable();
                $table->timestamps();
            });
            
            //Relationships
            Schema::table('attachments', function (Blueprint $table) {
                $table->foreign('profile_id')
                    ->references('id')
                    ->on('profiles')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('attachments');
    }
}
