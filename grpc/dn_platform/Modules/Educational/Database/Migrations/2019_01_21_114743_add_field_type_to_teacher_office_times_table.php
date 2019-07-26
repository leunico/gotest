<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldTypeToTeacherOfficeTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teacher_office_times', function (Blueprint $table) {
            $table->boolean('is_formal')->default(0)->comment('可约正式课')->after('time');
            $table->boolean('is_audition')->default(0)->comment('可约试听课')->after('time');
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
            $table->dropColumn("is_formal");
            $table->dropColumn("is_audition");
        });
    }
}
