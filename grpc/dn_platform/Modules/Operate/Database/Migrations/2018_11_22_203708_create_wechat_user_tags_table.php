<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatUserTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_user_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('wechat_tag_id')->comment('微信的标签id');
            $table->string('name',50)->comment('标签名');
            $table->unsignedTinyInteger('category')->comment('1:艺术编程，2:数字音乐');
            $table->boolean('useful')->default(true);
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
        Schema::dropIfExists('wechat_user_tags');
    }
}
