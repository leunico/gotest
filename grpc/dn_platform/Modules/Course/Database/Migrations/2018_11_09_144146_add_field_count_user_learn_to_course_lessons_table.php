<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldCountUserLearnToCourseLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->unsignedInteger('count_user_learns')->default(0)->comment('对外学习人数')->after('sort');
        });

        Schema::table('operation_logs', function (Blueprint $table) {
            $table->dropColumn('table');
            $table->string('table_name', 50)->comment('操作的表')->after('user_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn('count_user_learns');
        });
    }
}
