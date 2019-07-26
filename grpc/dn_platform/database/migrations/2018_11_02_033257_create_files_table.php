<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
        Schema::dropIfExists('files');
    }
}
