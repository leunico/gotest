<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldAttendanceToBiuniqueAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('biunique_appointments', function (Blueprint $table) {
            $table->unsignedTinyInteger('attendance')->default(0)->comment('考勤状态：0-未知，1-正常，2-缺勤')->after('star_cost');
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
            $table->dropColumn('attendance');
        });
    }
}
