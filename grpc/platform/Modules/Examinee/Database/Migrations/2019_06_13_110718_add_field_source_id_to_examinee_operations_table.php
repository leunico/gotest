<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldSourceIdToExamineeOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('examinee_operations', function (Blueprint $table) {
            $table->unsignedInteger('source_id')->default(0)->comment('来源')->after('examination_examinee_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('examinee_operations', function (Blueprint $table) {
            $table->dropColumn('source_id');
        });
    }
}
