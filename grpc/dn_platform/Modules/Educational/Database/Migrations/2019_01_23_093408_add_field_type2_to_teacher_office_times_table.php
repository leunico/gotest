<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldType2ToTeacherOfficeTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teacher_office_times', function (Blueprint $table) {
            $table->unsignedTinyInteger('type')->default(1)->comment('课程类型：1-正式，2-试听')->after('time');
            $table->timestamp('appointment_date')->nullable()->comment('可约日期')->after('time');
            $table->dropColumn('is_audition');
            $table->dropColumn('is_formal');
            $table->dropColumn('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teacher_office_times', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
