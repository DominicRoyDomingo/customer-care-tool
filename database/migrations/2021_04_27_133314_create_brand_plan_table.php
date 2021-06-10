<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_plan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('plan');
            $table->unsignedBigInteger('brand');

            $table->foreign('plan')
            ->references('id')
            ->on('payment_plan')
            ->onDelete('cascade');

            $table->foreign('brand')
            ->references('id')
            ->on('brands')
            ->onDelete('cascade');

            $table->timestamps();

            // php artisan migrate --path=/database/migrations/2021_04_27_133314_create_brand_plan_table.php
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brand_plan');
    }
}
