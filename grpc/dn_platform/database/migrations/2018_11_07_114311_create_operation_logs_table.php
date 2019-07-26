<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->string('user_name', 64)->comment('用户名');
            $table->string('table', 24)->comment('操作的表');
            $table->unsignedInteger('model_id')->comment('操作数据的id');
            $table->string('event', 16)->comment('当前操作的事件，create，update，delete等等');
            $table->string('route', 64)->comment('当前操作的URL');
            $table->string('description', 255)->default('')->comment('描述详情');
            $table->text('old')->comment('修改前');
            $table->text('new')->comment('修改后');
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
        Schema::dropIfExists('operation_logs');
    }
}
