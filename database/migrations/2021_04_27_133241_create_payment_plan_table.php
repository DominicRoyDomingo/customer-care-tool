<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_plan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name');
            $table->enum('type_plan', ['Bundle', 'Subscription']);
            $table->enum('status', ['Available', 'Unavailable','Hidden']);
            $table->float('price');
            $table->text('description');
            $table->text('features');
            $table->timestamps();

        });

        // php artisan migrate --path=/database/migrations/2021_04_27_133241_create_payment_plan_table.php
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_plan');
    }
}
