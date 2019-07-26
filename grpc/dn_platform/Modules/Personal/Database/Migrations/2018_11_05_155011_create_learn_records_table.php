<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLearnRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('learn_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户ID');
            $table->unsignedInteger('section_id')->default(0)->comment('环节id');
            $table->unsignedInteger('music_id')->default(0)->comment('乐理id');
            $table->timestamp('entry_at')->nullable()->comment('开始观看时间');
            $table->timestamp('leave_at')->nullable()->comment('结束观看时间');
            $table->integer('start_at')->default(0)->comment('视频开始时间，单位毫秒');
            $table->integer('end_at')->default(0)->comment('视频结束时间，单位毫秒');
            $table->integer('duration')->default(0)->comment('持续时间，单位毫秒');
            $table->index('user_id', 'user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('learn_records');
    }
}
