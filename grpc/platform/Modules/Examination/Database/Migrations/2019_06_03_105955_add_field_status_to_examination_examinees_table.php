<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldStatusToExaminationExamineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('examination_examinees', function (Blueprint $table) {
            $table->boolean('status')->default(0)->comment('是否确认')->after('rank');
            // $table->unique('admission_ticket');
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
            $table->dropColumn('status');
        });
    }
}
