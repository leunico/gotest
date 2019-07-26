<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldLessonSortToBiuniqueAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('biunique_appointments', function (Blueprint $table) {
            $table->unsignedInteger('lesson_sort')->default(0)->comment('第几课时')->after('biunique_course_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('biunique_appointments', function (Blueprint $table) {
            $table->dropColumn('lesson_sort');
        });
    }
}
