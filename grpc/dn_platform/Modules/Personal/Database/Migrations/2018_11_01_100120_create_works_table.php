<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorksTable extends Migration
{
    protected $table = 'works';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 50)->comment('作品标题');
            $table->unsignedInteger('user_id')->default(0)->comment('创建人');
            $table->string('image_cover')->default('')->comment('封面图');
            $table->string('description')->default('')->comment('描述');
            $table->string('type', 20)->default('')->comment('作品类型');
            $table->string('board_type', 20)->default('')->comment('板子类型');
            $table->unsignedInteger('lesson_id')->default(0)->comment('主题id');
            $table->string('file_url')->default('')->comment('作品内容');
            $table->unsignedInteger('views')->default(0)->comment('浏览数');
            $table->unsignedInteger('comments')->default(0)->comment('评论数');
            $table->unsignedInteger('likes')->default(0)->comment('点赞数');
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
            $table->index('title');
            $table->index('lesson_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
