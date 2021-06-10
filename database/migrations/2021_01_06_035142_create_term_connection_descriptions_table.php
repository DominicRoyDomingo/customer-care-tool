<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTermConnectionDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('term_connection_descriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('term_id')->nullable();
            $table->unsignedBigInteger('linked_term_id')->nullable();
            $table->unsignedBigInteger('description_id')->nullable();
            $table->enum('methods', ['mutual', 'none-mutual', 'none']);
            $table->timestamps();

            $table->foreign('description_id')
                ->references('id')
                ->on('connection_descriptions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('term_connection_descriptions');
    }
}
