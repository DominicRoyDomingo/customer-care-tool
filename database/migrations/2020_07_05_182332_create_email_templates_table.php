<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable( 'email_templates' ) ) {
            Schema::create('email_templates', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('template_name');
                $table->text('variables');
                $table->text('html_content')->nullable();
                $table->text('others');
                $table->text('preview')->nullable();
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
        Schema::dropIfExists('email_templates');
    }
}
