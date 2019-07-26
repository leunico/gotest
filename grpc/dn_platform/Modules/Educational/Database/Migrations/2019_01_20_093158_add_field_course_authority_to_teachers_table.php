<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldCourseAuthorityToTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->unsignedInteger('course_authority')->default(0)->comment('正式课权限')->after('authority');
            $table->boolean('type')->default(1)->comment('老师类型：1-兼职，2-全职')->after('user_id');
            $table->string('number', 200)->default('')->comment('老师编号')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('course_authority');
            $table->dropColumn('type');
            $table->dropColumn('number');
        });
    }
}
