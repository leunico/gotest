<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DelFiledUnlockDayToCourseLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('big_course_course_pivot', function (Blueprint $table) {
            $table->dropColumn('unlock_day');
        });

        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn('unlock_day');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
