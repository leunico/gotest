<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('名称');
            $table->tinyInteger('category')->nullable()->comment('标签类型：1-音乐练耳');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
        });

        Schema::create('model_has_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('model_id')->comment('关联模型');
            $table->string('model_type', 20)->nullable()->comment('模型分类');
            $table->unsignedInteger('tag_id')->comment('所属标签');

            $table->index(['model_id', 'model_type']);
            $table->index('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
        Schema::dropIfExists('model_tag_pivot');
    }
}
