<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldCategoryToMajorProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('major_problems', function (Blueprint $table) {
            $table->unsignedTinyInteger('category')->comment('题目类型：1-单选题，2-判断题，3-填空题，4-操作题')->after('examination_id');
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
            $table->dropColumn('category');
        });
    }
}
