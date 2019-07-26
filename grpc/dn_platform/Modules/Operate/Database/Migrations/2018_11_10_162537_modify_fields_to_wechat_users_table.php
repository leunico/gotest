<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyFieldsToWechatUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wechat_users', function (Blueprint $table) {
            $table->dropColumn('openid');
            $table->dropColumn('remark');
            $table->dropColumn('groupid');
            $table->dropColumn('tagid_list');
            $table->dropColumn('qr_scene');
            $table->dropColumn('qr_scene_str');
            $table->dropColumn('subscribe_scene');
            $table->dropColumn('category');
            $table->dropColumn('subscribe');
            $table->dropColumn('subscribe_time');
            $table->dropColumn('unsubscribe_time');

            $table->string('unionid',50)->nullable()->unique();
            $table->string('art_openid', 28)->nullable();
            $table->string('music_openid', 28)->nullable();
            $table->string('website_openid', 50)->nullable();
            $table->string('mini_program_openid', 50)->nullable();
            $table->boolean('art_subscribe')->default(false);
            $table->boolean('music_subscribe')->default(false);

        });

        Schema::create('wechat_user_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('wechat_user_id');
            $table->unsignedTinyInteger('category')->comment('1迪恩艺术，2数字音乐');
            $table->string('remark')->nullable()->comment('备注');
            $table->unsignedInteger('groupid');
            $table->string('tagid_list')->comment('所属标签');
            $table->string('subscribe_scene', 100)->comment('用户关注的渠道来源');
            $table->unsignedInteger('qr_scene')->default(0);
            $table->string('qr_scene_str');
            $table->timestamp('subscribe_time')->nullable()->comment('关注时间');
            $table->timestamp('unsubscribe_time')->nullable()->comment('取消关注时间');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('unionid', 50)->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wechat_users', function (Blueprint $table) {
            $table->timestamp('subscribe_time')->nullable()->comment('关注时间');
            $table->timestamp('unsubscribe_time')->nullable()->comment('取消关注时间');
            $table->string('remark')->nullable()->comment('备注');
            $table->unsignedInteger('groupid');
            $table->string('tagid_list')->comment('所属标签');
            $table->string('subscribe_scene', 100)->comment('用户关注的渠道来源');
            $table->unsignedInteger('qr_scene')->default(0);
            $table->string('qr_scene_str');
            $table->boolean('subscribe');
            $table->string('openid', 28)->comment('openid');
            $table->unsignedTinyInteger('category')->comment('类别，0迪恩艺术，1数字音乐');

            $table->dropColumn('unionid');
            $table->dropColumn('art_openid');
            $table->dropColumn('music_openid');
            $table->dropColumn('website_openid');
            $table->dropColumn('mini_program_openid');
            $table->dropColumn('art_subscribe');
            $table->dropColumn('music_subscribe');
        });

        Schema::dropIfExists('wechat_user_details');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('unionid');
        });
    }
}
