<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DelFieldAverageScoreToMajorProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('major_problems', function (Blueprint $table) {
            $table->dropColumn('avg_score');
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
            $table->unsignedSmallInteger('avg_score')->default(0)->comment('平均分数')->after('category');
        });
    }
}
