<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublishingHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'publishing_history' ) ) {
            Schema::create('publishing_history', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('publishing_id');
                $table->unsignedBigInteger('publisher');
                $table->unsignedBigInteger('author');
                $table->unsignedBigInteger('platform');
                $table->unsignedBigInteger('status');
                $table->text('notes')->nullable();
                $table->timestamps();
            });

            Schema::table('publishing_history', function (Blueprint $table) {
                $table->foreign('publishing_id')->references('id')->on('publishing')->onDelete('cascade');
                $table->foreign('publisher')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('author')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('platform')->references('id')->on('platform')->onDelete('cascade');
                $table->foreign('status')->references('id')->on('date_statuses')->onDelete('cascade');
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
        Schema::dropIfExists('publishing_history');
    }
}
