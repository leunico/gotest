<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvestmentsPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('permissions')->insert([
            'name' => 'investment-manage',
            'guard_name' => 'api',
            'category' => '用户管理',
            'title' => '投资机构管理',
            'description' => '投资机构查看、新增、编辑',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        \DB::table('permissions')->insert([
            'name' => 'course-unlock-no-limit',
            'guard_name' => 'api',
            'category' => '课程管理',
            'title' => '前端观看课程不受解锁规则限制',
            'description' => '拥有该权限的用户，前端观看课程不受解锁规则限制',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
