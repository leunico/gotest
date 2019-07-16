<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldAchievementStatusToExaminationExamineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('examination_examinees', function (Blueprint $table) {
            $table->boolean('achievement_status')->default(1)->comment('成绩状态')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('examination_examinees', function (Blueprint $table) {
            $table->dropColumn('achievement_status');
        });
    }
}
