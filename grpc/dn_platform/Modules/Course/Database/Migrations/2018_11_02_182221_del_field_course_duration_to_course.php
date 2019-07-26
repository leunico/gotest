<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DelFieldCourseDurationToCourse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('course_duration');
        });

        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn('work_id');
            $table->dropColumn('is_public');
            $table->dropColumn('knowledge');
            $table->unsignedInteger('course_id')->nullable()->comment('课程id')->after('title');
            $table->string('tutorial_link')->nullable()->comment('学习指南')->after('cover_id');
            $table->text('materials')->nullable()->comment('材料清单')->after('cover_id');

            $table->index('course_id');
        });

        Schema::table('course_sections', function (Blueprint $table) {
            $table->string('source_link')->nullable()->comment('资源链接')->after('category');
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
