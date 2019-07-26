<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldStatusToAuditionClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audition_classes', function (Blueprint $table) {
            $table->boolean('status')->default(1)->comment('是否有效：0-否，1-是')->after('remark');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audition_classes', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
