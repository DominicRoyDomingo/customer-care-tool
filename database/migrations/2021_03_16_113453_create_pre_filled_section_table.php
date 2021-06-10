<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreFilledSectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_filled_section', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name')->nullable();
            $table->bigInteger('tag_used_id')->unsigned();
            $table->bigInteger('type_of_publishing_item_id')->unsigned();
            $table->timestamps();

            $table->foreign('tag_used_id')
                ->references('id')
                ->on('html_tags')
                ->onDelete('cascade');

            $table->foreign('type_of_publishing_item_id')
                ->references('id')
                ->on('publishing_item_type')
                ->onDelete('cascade');
        });

        // CREATE TABLE `pre_filled_section` (
        //     `id` bigint(20) NOT NULL,
        //     `name` text DEFAULT NULL,
        //     `tag_used_id` int(11) NOT NULL,
        //     `type_of_publishing_item_id` int(11) NOT NULL,
        //     `created_at` timestamp NULL DEFAULT NULL,
        //     `updated_at` timestamp NULL DEFAULT NULL
        //   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
          
        //   ALTER TABLE `pre_filled_section`
        //     ADD PRIMARY KEY (`id`),
        //     ADD KEY `tag_used_id` (`tag_used_id`),
        //     ADD KEY `type_of_publishing_item_id` (`type_of_publishing_item_id`);
            
        //   --
        //   ALTER TABLE `pre_filled_section`
        //     MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

        // ALTER TABLE `pre_filled_section`
        // ADD CONSTRAINT `pre_filled_section_ibfk_1` FOREIGN KEY (`tag_used_id`) REFERENCES `html_tags` (`id`),
        // ADD CONSTRAINT `pre_filled_section_ibfk_2` FOREIGN KEY (`type_of_publishing_item_id`) REFERENCES `publishing_item_type` (`id`);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pre_filled_section');
    }
}
