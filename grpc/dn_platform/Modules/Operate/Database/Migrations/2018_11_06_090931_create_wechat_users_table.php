<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('category')->comment('类别，0迪恩艺术，1数字音乐');
            $table->boolean('subscribe')->default(true)->comment('是否关注');
            $table->string('openid', 28)->comment('openid');
            $table->string('nickname', 100)->comment('用户昵称');
            $table->boolean('sex')->default(true)->comment('性别：0女，1男');
            $table->string('language', 50)->comment('语言');
            $table->string('city', 50)->comment('城市');
            $table->string('province', 50)->comment('省份');
            $table->string('country', 50)->comment('国家');
            $table->string('headimgurl')->comment('头像地址');
            $table->timestamp('subscribe_time')->nullable()->comment('关注时间');
            $table->timestamp('unsubscribe_time')->nullable()->comment('取消关注时间');
            $table->string('remark')->nullable()->comment('备注');
            $table->unsignedInteger('groupid');
            $table->string('tagid_list')->comment('所属标签');
            $table->string('subscribe_scene', 100)->comment('用户关注的渠道来源');
            $table->unsignedInteger('qr_scene')->default(0);
            $table->string('qr_scene_str');
            $table->timestamps();

            $table->unique(['category', 'openid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wechat_users');
    }
}
