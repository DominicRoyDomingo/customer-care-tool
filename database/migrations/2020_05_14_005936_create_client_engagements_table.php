<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientEngagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'client_engagements' ) ) {
            Schema::create('client_engagements', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('profile_id');
                $table->unsignedBigInteger('engagement_id');
                $table->unsignedBigInteger('platform_id')->nullable();
                $table->unsignedBigInteger('added_by');
                $table->longText('details')->nullable();
                $table->date('engagement_date');
                $table->timestamps();
            });

            //Relationships
            Schema::table('client_engagements', function (Blueprint $table) {
                
                $table->foreign('engagement_id')
                    ->references('id')
                    ->on('engagements')
                    ->onDelete('cascade');
                
                $table->foreign('platform_id')
                    ->references('id')
                    ->on('platform')
                    ->onDelete('cascade');

                $table->foreign('added_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

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
        Schema::dropIfExists('client_engagements');
    }
}
