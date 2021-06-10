<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('choices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('value');
            $table->timestamps();
        });

        // CREATE TABLE `choices` (
        //     `id` bigint(20) NOT NULL,
        //     `value` text DEFAULT NULL,
        //     `created_at` timestamp NULL DEFAULT NULL,
        //     `updated_at` timestamp NULL DEFAULT NULL
        //   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
          
        // ALTER TABLE `choices` ADD PRIMARY KEY (`id`);
        // ALTER TABLE `choices`MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('choices');
    }
}
