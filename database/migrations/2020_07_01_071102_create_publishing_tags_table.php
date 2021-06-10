<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublishingTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'publishing_tags' ) ) {
            Schema::create('publishing_tags', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('publishing_id');
                $table->unsignedBigInteger('tag');
                $table->timestamps();
            });

            Schema::table('publishing_tags', function (Blueprint $table) {
                $table->foreign('publishing_id')->references('id')->on('publishing')->onDelete('cascade');
                $table->foreign('tag')->references('id')->on('tags')->onDelete('cascade');
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
        Schema::dropIfExists('publishing_tags');
    }
}
