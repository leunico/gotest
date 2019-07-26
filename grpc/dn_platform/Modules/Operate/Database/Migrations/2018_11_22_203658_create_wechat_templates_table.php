<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tpl_id', 60)->comment('模板id');
            $table->string('title',30)->comment('模板标题');
            $table->string('content', 1000)->comment('详细内容');
            $table->unsignedTinyInteger('category')->comment('1:艺术编程，2数字音乐');
            $table->boolean('useful')->default(true)->comment('是否可用');

            $table->unique(['tpl_id','category']);
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
        Schema::dropIfExists('wechat_templates');
    }
}
