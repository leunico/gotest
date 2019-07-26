<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatPushUserLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_push_user_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('category')->default(1)->comment('1:艺术编程，2:数字音乐');
            $table->unsignedInteger('wechat_push_job_id')->comment('推送id，对应wechat_push_jobs');
            $table->string('openid', 28)->comment('openid');

            $table->timestamps();
        });

        Schema::table('wechat_push_jobs', function (Blueprint $table) {
            $table->unsignedTinyInteger('category')->default(1)->comment('1：艺术编程，2：数字音乐')->after('wechat_template_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wechat_push_user_logs');
        Schema::table('wechat_push_jobs', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
}
