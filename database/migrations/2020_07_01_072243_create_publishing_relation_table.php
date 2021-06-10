<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublishingRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'publishing_relation' ) ) {
            Schema::create('publishing_relation', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('publishing_a');
                $table->unsignedBigInteger('publishing_b');
                $table->enum('status', array('No Relation', 'To Be Linked'));
                $table->timestamps();
            });

            Schema::table('publishing_relation', function (Blueprint $table) {
                $table->foreign('publishing_a')->references('id')->on('publishing')->onDelete('cascade');
                $table->foreign('publishing_b')->references('id')->on('publishing')->onDelete('cascade');
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
        Schema::dropIfExists('publishing_relation');
    }
}
