<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->index()->comment('名称');
            $table->integer('file_id')->unsigned()->default(0)->comment('图片地址');
            $table->string('keywords',255)->comment('关键字');
            $table->string('description',500)->comment('描述');
            $table->text('body')->comment('内容');
            $table->tinyInteger('status')->unsigned()->default(0);
            $table->integer('operate_id')->unsigned()->default(0);
            $table->integer('views')->unsigned()->default(0)->comment('浏览量');
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
        Schema::dropIfExists('articles');
    }
}
