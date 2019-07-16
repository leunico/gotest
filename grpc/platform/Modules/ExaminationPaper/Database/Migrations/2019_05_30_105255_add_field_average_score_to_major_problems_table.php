<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldAverageScoreToMajorProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('major_problems', function (Blueprint $table) {
            $table->unsignedSmallInteger('avg_score')->default(0)->comment('平均分数')->after('category');
        });

        Schema::table('examination_examinees', function (Blueprint $table) {
            $table->dropColumn('verification_file');
            $table->boolean('testing_status')->default(0)->comment('考前检测状态')->after('rank');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('major_problems', function (Blueprint $table) {
            $table->dropColumn('avg_score');
        });

        Schema::table('major_problems', function (Blueprint $table) {
            $table->dropColumn('testing_status');
            $table->unsignedInteger('verification_file')->default(0)->comment('本次考试验证文件（头像）');
        });
    }
}
