<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'content_history' ) ) {
            Schema::create('content_history', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('content');
                $table->unsignedBigInteger('author_task');
                $table->unsignedBigInteger('author_idea');
                $table->unsignedBigInteger('status');
                $table->text('notes')->nullable();
                $table->timestamps();
            });

            Schema::table('content_history', function (Blueprint $table) {
                $table->foreign('content')->references('id')->on('content')->onDelete('cascade');
                $table->foreign('author_task')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('author_idea')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('content_history');
    }
}
