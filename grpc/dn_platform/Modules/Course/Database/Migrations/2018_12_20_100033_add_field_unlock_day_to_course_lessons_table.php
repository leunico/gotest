<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldUnlockDayToCourseLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('cycle');
        });

        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn('unlock_sort');
            $table->unsignedSmallInteger('unlock_day')->default(10000)->comment('解锁的延迟天数')->after('sort');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedTinyInteger('cycle')->default(0)->comment('学习周期')->after('level');
        });

        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn('unlock_day');
            $table->unsignedTinyInteger('unlock_sort')->default(0)->comment('解锁的批次')->after('sort');
        });
    }
}
