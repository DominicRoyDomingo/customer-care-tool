<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublishingItemContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publishing_item_content', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('publishing_item');
            $table->text('content')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('slug')->nullable();
            $table->text('alt_tag_image')->nullable();
            $table->text('img_description')->nullable();
            $table->timestamps();

            $table->foreign('publishing_item')
                ->references('id')
                ->on('medical_articles')
                ->onDelete('cascade');
        });

        // SQL Script
        // CREATE TABLE `publishing_item_content` (
        //     `id` bigint(20) NOT NULL,
        //     `publishing_item` int(11) NOT NULL,
        //     `content` text(5000) DEFAULT NULL,
        //     `meta_description` text(5000) DEFAULT NULL,
        //     `slug` text(5000) DEFAULT NULL,
        //     `alt_tag_image` text(5000) DEFAULT NULL,
        //     `img_description` text(5000) DEFAULT NULL,
        //     `created_at` timestamp NULL DEFAULT NULL,
        //     `updated_at` timestamp NULL DEFAULT NULL
        //   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
          
        //   ALTER TABLE `publishing_item_content`
        //     ADD PRIMARY KEY (`id`),
        //     ADD KEY `publishing_item` (`publishing_item`);
            
        //   --
        //   ALTER TABLE `publishing_item_content`
        //     MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

        // ALTER TABLE `publishing_item_content`
        // ADD CONSTRAINT `publishing_item_content_ibfk_1` FOREIGN KEY (`publishing_item`) REFERENCES `medical_articles` (`id`);

        // php artisan migrate --path=/database/migrations/2021_03_16_112752_create_publishing_item_content_table.php
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publishing_item_content');
    }
}
