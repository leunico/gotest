<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mobile',11)->comment('手机号');
            $table->string('content',1000)->comment('短信内容');
            $table->unsignedTinyInteger('service')->default(0)->comment('服务商，1：云片网');
            $table->string('sid',100)->comment('短信id');
            $table->unsignedTinyInteger('send_status')->default(1)->comment('发送状态 0:fail,1:success');
            $table->string('error_code',10)->nullable()->comment('发送返回的错误码');
            $table->string('error_msg', 50)->nullable()->comment('运营商返回的代码');
            $table->timestamp('receive_at')->nullable()->comment('接收时间');
            $table->unsignedTinyInteger('count')->default(1)->comment('短信数量');
            $table->string('template', 50)->nullable()->comment('模板id');
            $table->string('template_params', 1000)->nullable()->comment('模板参数');
            $table->timestamps();

            $table->unique(['service','sid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_logs');
    }
}
