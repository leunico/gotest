<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examinees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20)->comment('名字');
            $table->string('contacts', 20)->comment('联系人');
            $table->string('email', 100)->nullable()->comment('电子邮箱');
            $table->string('phone', 50)->nullable()->comment('手机号码');
            $table->string('password')->comment('密码');
            $table->unsignedTinyInteger('certificate_type')->default(1)->comment('证件类型：1-身份证,2-护照');
            $table->string('certificates', 20)->comment('证件号码');
            $table->tinyInteger('sex')->default(0)->comment('用户性别：0-未知,1-男,2-女');
            $table->boolean('status')->default(1)->comment('是否有效');
            $table->unsignedTinyInteger('source')->default(0)->comment('来源：0-未知,1-录入,2-导入表,3-用户池');
            $table->string('remarks', 500)->nullable()->comment('备注');
            $table->unsignedInteger('creator_id')->default(0)->comment('创建人');

            $table->softDeletes();
            $table->timestamps();
            // $table->unique('phone');
            // $table->unique('email');
            $table->unique('certificates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examinees');
    }
}
