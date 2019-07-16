<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateExaminationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examinations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('creator_id')->default(0)->comment('创建人');
            $table->unsignedInteger('match_id')->comment('所属赛事');
            $table->unsignedInteger('examination_category_id')->comment('考试类型');
            $table->string('title')->comment('名称');
            $table->string('examination_paper_title')->default('')->comment('试卷名称');
            $table->timestamp('start_at')->nullable()->comment('开始时间');
            $table->timestamp('end_at')->nullable()->comment('结束时间');
            $table->unsignedTinyInteger('age_min')->default(0)->comment('最小年龄');
            $table->unsignedTinyInteger('age_max')->default(0)->comment('最大年龄');
            $table->boolean('status')->default(0)->comment('是否发布');
            $table->string('description', 500)->default('')->comment('考试介绍');
            $table->unsignedInteger('exam_file_id')->default(0)->comment('考试须知');

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('examination_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category')->comment('分类');
            $table->string('title')->comment('考试类型');
        });

        $categorys = [
            [
                'category' => 'Scratch',
                'title' => 'Scratch创意编程初级组（1-4年级）'
            ],
            [
                'category' => 'Scratch',
                'title' => 'Scratch创意编程高级组（5-8年级）'
            ],
            [
                'category' => 'Python',
                'title' => 'Python创意编程组（7-12年级）'
            ],
            [
                'category' => 'C++',
                'title' => 'C++创意编程组（7-12年级）'
            ],
            [
                'category' => 'Minecraft',
                'title' => 'Minecraft创意编程初级组（1-4年级）'
            ],
            [
                'category' => 'Minecraft',
                'title' => 'Minecraft创意编程高级组（5-9年级）'
            ],
        ];

        DB::table('examination_categories')->insert($categorys);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examinations');
        Schema::dropIfExists('examination_categories');
    }
}
