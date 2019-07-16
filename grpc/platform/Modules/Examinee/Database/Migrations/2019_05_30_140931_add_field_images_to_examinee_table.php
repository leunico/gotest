<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldImagesToExamineeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('examinees', function (Blueprint $table) {
            $table->char('birth', 10)->default('')->comment('出生日期')->after('sex');
            $table->string('photo')->comment('照片')->after('sex');
            $table->string('certificates_photos', 1000)->default('')->comment('证件照片')->after('certificate_type');
            $table->string('school_name', 100)->default('')->comment('学校名称')->after('sex');
            $table->unsignedInteger('city')->default(0)->comment('城市编号')->after('sex');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('examinees', function (Blueprint $table) {
            $table->dropColumn('birth');
            $table->dropColumn('photo');
            $table->dropColumn('certificates_photos');
            $table->dropColumn('school_name');
            $table->dropColumn('city');
        });
    }
}
