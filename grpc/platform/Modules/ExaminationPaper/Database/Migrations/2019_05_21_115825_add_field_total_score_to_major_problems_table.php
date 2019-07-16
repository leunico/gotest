<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldTotalScoreToMajorProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('major_problems', function (Blueprint $table) {
            $table->unsignedSmallInteger('total_score')->default(0)->comment('总分')->after('sort');
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
            $table->dropColumn('total_score');
        });
    }
}
