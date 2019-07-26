<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSqlIndexToCourse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('big_course_course_pivot', function (Blueprint $table) {
            $table->index('course_id');
            $table->index('big_course_id');
        });

        Schema::table('course_lessons', function (Blueprint $table) {
            $table->index(['course_id', 'sort']);
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
            $table->dropIndex('course_id');
            $table->dropIndex('big_course_id');
        });

        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropIndex(['course_id', 'sort']);
        });
    }
}
