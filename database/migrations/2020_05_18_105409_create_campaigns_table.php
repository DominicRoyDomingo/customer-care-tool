<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'campaigns' ) ) {
            Schema::create('campaigns', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->text('campaign')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->unsignedBigInteger('brand_id')->nullable();
                $table->dateTime('sent_at')->nullable();
                $table->timestamps();

                $table->foreign('created_by')
                    ->references('id')
                    ->on('users');
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
        Schema::dropIfExists('campaigns');
    }
}
