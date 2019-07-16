<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacePlusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('face_analyses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('face_token', 32)->comment('人脸的标识');
            $table->string('image_id', 50)->comment('被检测的图片在系统中的标识');
            $table->char('gender', 6)->comment('性别分析结果');
            $table->unsignedTinyInteger('age')->comment('年龄分析结果');
            $table->string('headpose', 100)->comment('转头分析结果');
            $table->string('blur')->comment('人脸模糊分析结果');
            $table->string('eyegaze_left')->comment('左眼睛状态信息分析结果');
            $table->string('eyegaze_right')->comment('右眼睛状态信息分析结果');

            $table->timestamps();
        });

        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hash', 150)->comment('文件 hash');
            $table->string('origin_filename', 100)->nullbale()->default(null)->comment('原始文件名');
            $table->string('driver_baseurl', 100)->default('')->comment('驱动器基础域名');
            $table->string('filename', 100)->comment('文件名');
            $table->string('mime', 50)->comment('mime type');
            $table->float('width')->nullbale()->default(null)->comment('图片宽度');
            $table->float('height')->nullbale()->default(null)->comment('图片高度');
            $table->timestamps();

            # 索引
            $table->unique('hash');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('face_plus');
        Schema::dropIfExists('files');
    }
}
