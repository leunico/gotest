<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMusicPracticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('music_practices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('名称');
            $table->string('audio_link')->nullable()->comment('音频地址');
            $table->unsignedInteger('book_id')->nullable()->comment('乐谱文件');
            $table->integer('sort')->default(0)->comment('排序');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：0-下架，1-上架');
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
        Schema::dropIfExists('music_practices');
    }
}
