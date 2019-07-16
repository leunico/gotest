<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldStartTimeToExaminationExamineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('examination_examinees', function (Blueprint $table) {
            $table->timestamp('start_time')->nullable()->comment('开始考试时间')->after('is_hand');
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
            $table->dropColumn('start_time');
        });
    }
}
