<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyncReferenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_reference', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sync_class');
            $table->enum('type_of_data', array('Update', 'New', 'Delete'));
            $table->string('source_table');
            $table->unsignedBigInteger('table_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sync_reference');
    }
}
