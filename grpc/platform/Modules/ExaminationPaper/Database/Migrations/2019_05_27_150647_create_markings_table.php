<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarkingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('examination_examinees', function (Blueprint $table) {
            $table->unsignedSmallInteger('objective_score')->default(0)->comment('客观题得分')->after('hand_time');
            $table->unsignedSmallInteger('subjective_score')->default(0)->comment('主观题得分')->after('hand_time');
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
            $table->dropColumn('objective_score');
            $table->dropColumn('subjective_score');
        });
    }
}
