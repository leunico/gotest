<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('number')->comment('banner编号');
            $table->unsignedTinyInteger('category')->comment('类别：1-链接，2-无法点击，3-弹窗，4-表单，5-视频');
            $table->unsignedInteger('file_id')->nullable()->comment('banner文件');
            $table->string('link')->nullable()->comment('链接');
            $table->unsignedTinyInteger('platform')->nullable()->comment('显示平台：1-电脑端，2-移动端');
            $table->boolean('status')->default(1)->comment('是否有效：0-无，1-有');

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
        Schema::dropIfExists('banners');
    }
}
