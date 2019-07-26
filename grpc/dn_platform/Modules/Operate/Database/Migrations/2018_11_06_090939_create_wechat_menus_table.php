<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_menus', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id')->default(0)->comment('上级菜单id');
            $table->string('type')->nullable()->comment('菜单的响应动作类型');
            $table->boolean('is_conditional')->default(false)->comment('0普通菜单，1个性菜单');
            $table->unsignedInteger('matchrule_id')->default(0)->comment('个性标签id');
            $table->string('name', 100)->comment('菜单名称');
            $table->text('ext')->comment('其他参数');
            $table->boolean('is_public')->default(false)->comment('是否显示');
            $table->unsignedTinyInteger('sort')->default(0)->comment('排序');
            $table->boolean('category')->default(false)->comment('0迪恩艺术，1数字音乐');
            $table->timestamps();
            $table->softDeletes();

            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wechat_menus');
    }
}
