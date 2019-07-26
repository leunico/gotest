<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiuniqueCourseResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biunique_course_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 200)->comment('标题');
            $table->unsignedInteger('biunique_course_id')->default(0)->comment('所属一对一课程');
            $table->unsignedInteger('file_id')->comment('资源id');
            $table->unsignedTinyInteger('category')->default(1)->comment('资源类型：1-图片，2-视频，3-音频');
            $table->boolean('status')->default(1)->comment('是否上下架');

            $table->timestamps();
            $table->index('biunique_course_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('biunique_course_resources');
    }
}
