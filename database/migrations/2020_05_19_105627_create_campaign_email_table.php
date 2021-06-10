<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignEmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'campaign_email' ) ) {
            Schema::create('campaign_email', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('sender_name')->nullable();
                $table->string('sender_email')->nullable();
                $table->string('subject')->nullable();
                $table->text('content')->nullable();
                $table->enum('type', ['plain', 'html']);
                $table->enum('status', ['sent', 'draft']);
                $table->unsignedBigInteger('campaign_id');
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
        Schema::dropIfExists('campaign_email');
    }
}
