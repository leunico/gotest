<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMusicTheoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('music_theories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('乐理名称');
            $table->unsignedInteger('file_id')->nullable()->comment('文件');
            $table->unsignedTinyInteger('status')->default(1)->comment('乐理状态：0-下架，1-上架');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('course_music_theory_pivot', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_id')->comment('所属系列课');
            $table->unsignedInteger('music_theory_id')->comment('所属乐理');
            $table->timestamps();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_drainage')->default(0)->comment('是否引流课：0-否，1-是')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('music_theories');
        Schema::dropIfExists('course_music_theory_pivot');
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('is_drainage');
        });
    }
}
