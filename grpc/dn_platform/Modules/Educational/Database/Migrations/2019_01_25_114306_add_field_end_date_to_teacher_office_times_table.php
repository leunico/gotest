<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldEndDateToTeacherOfficeTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teacher_office_times', function (Blueprint $table) {
            $table->timestamp('end_date')->nullable()->comment('结束时间')->after('appointment_date');
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
            $table->dropColumn('end_date');
        });
    }
}
