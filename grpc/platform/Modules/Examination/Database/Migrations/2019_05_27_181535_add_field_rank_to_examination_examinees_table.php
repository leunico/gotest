<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldRankToExaminationExamineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('examination_examinees', function (Blueprint $table) {
            $table->unsignedSmallInteger('rank')->default(0)->comment('排名')->after('objective_score');
            $table->index('rank');
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
            $table->dropColumn('rank');
        });
    }
}
