<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuditionClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audition_classes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->unsignedInteger('teacher_id')->comment('老师id');
            $table->unsignedInteger('creator_id')->default(0)->comment('创建人id');
            $table->unsignedTinyInteger('category')->default(1)->comment('试听课程类型：1-乐理，2-视唱练耳');
            $table->timestamp('entry_at')->nullable()->comment('开始上课时间');
            $table->timestamp('leave_at')->nullable()->comment('结束上课时间');
            $table->string('remark')->default('')->comment('备注');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audition_classes');
    }
}
