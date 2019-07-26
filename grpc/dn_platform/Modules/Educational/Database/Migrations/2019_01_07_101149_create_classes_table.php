<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CreateClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('班级名称');
            $table->timestamp('entry_at')->nullable()->comment('开始上课时间');
            $table->timestamp('leave_at')->nullable()->comment('结束上课时间');
            $table->unsignedTinyInteger('category')->default(1)->comment('班级类型：1-大课，2-系列');
            $table->unsignedTinyInteger('pattern')->default(1)->comment('解锁模式');
            $table->unsignedTinyInteger('frequency')->default(1)->comment('频率');
            $table->json('unlocak_times')->comment('解锁时间');
            $table->unsignedInteger('big_course_id')->default(0)->comment('所属大课');
            $table->unsignedInteger('course_id')->default(0)->comment('所属课程');
            $table->unsignedInteger('teacher_id')->default(0)->comment('老师id');
            $table->boolean('status')->default(0)->comment('是否发布');

            $table->timestamps();
            $table->softDeletes();

            DB::table('roles')->insert(['name' => 'course_teacher', 'guard_name' => 'api', 'title' => '教务运营', 'description' => '教务运营', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classes');
    }
}
