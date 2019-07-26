<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldIsMailToCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_mail')->default(0)->comment('是否寄件：0-否，1-是')->after('status');
        });

        Schema::table('course_sections', function (Blueprint $table) {
            $table->unsignedInteger('source_duration')->default(0)->comment('资源时长（视频、音频）')->after('source_link');
        });

        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn('is_mail');
            $table->unsignedTinyInteger('is_code')->default(0)->comment('是否编程：0-否，1-是')->after('status');
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
            $table->dropColumn('is_mail');
        });

        Schema::table('course_sections', function (Blueprint $table) {
            $table->dropColumn('source_duration');
        });

        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn('is_code');
            $table->unsignedTinyInteger('is_mail')->default(0)->comment('是否寄件：0-否，1-是')->after('status');
        });
    }
}
