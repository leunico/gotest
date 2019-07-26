<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_lessons', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('delivery_id')->comment('寄件记录id');
            $table->unsignedInteger('lesson_id')->comment('主题id');

            $table->unique(['delivery_id','lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_lessons');
    }
}
