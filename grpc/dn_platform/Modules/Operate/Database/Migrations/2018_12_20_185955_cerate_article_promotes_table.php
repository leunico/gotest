<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CerateArticlePromotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_promotes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('软文标题');
            $table->unsignedInteger('article_id')->comment('软文编号');
            $table->unsignedInteger('image_id')->comment('添加码[图片]');
            $table->string('name', 100)->default('')->comment('个人名称');
            $table->string('wechat_number', 100)->default('')->comment('微信号');
            $table->boolean('status')->default(0)->comment('生效状态：0|1');
            $table->unsignedInteger('pv')->default(0)->comment('浏览量');
            $table->unsignedInteger('uv')->default(0)->comment('用户量');

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
        Schema::dropIfExists('article_promotes');
    }
}
