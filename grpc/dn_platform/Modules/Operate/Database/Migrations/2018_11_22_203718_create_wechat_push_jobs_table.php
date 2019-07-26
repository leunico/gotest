<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatPushJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_push_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('wechat_template_id')->comment('模板id，对应wechat_templates');
            $table->timestamp('push_at')->useCurrent()->comment('推送时间');
            $table->string('tpl_params', 1500)->comment('模板参数');
            $table->string('url',200)->default('')->comment('模板链接');
            $table->unsignedInteger('creator_id')->default(0)->comment('创建人');
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
        Schema::dropIfExists('wechat_push_jobs');
    }
}
