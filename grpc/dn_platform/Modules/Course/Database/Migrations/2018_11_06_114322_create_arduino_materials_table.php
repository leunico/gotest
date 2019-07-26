<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArduinoMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arduino_materials', function (Blueprint $table) {
            $table->increments('id');
            $table->string('info', 100)->comment('信息');
            $table->string('name', 100)->comment('名字');
            $table->string('md5', 100)->nullable()->comment('md5文件');
            $table->string('source_link')->nullable()->comment('资源链接');
            $table->unsignedTinyInteger('is_arduino')->nullable()->comment('元件-1，功能件-2');
            $table->unsignedInteger('sort')->nullable()->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arduino_materials');
    }
}
