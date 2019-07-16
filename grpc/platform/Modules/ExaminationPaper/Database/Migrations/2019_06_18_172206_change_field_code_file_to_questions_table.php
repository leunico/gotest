<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldCodeFileToQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('code_file');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->string('code_file')->default('')->comment('预加载文件')->after('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('code_file');
            $table->unsignedInteger('code_file')->default(0)->comment('预加载文件')->after('code');
        });
    }
}
