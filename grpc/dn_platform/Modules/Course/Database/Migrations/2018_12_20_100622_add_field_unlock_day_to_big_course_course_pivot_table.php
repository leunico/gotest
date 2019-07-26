<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldUnlockDayToBigCourseCoursePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('big_course_course_pivot', function (Blueprint $table) {
            $table->unsignedSmallInteger('unlock_day')->default(0)->comment('解锁的延迟天数')->after('sort');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('big_course_course_pivot', function (Blueprint $table) {
            $table->dropColumn('unlock_day');
        });
    }
}
