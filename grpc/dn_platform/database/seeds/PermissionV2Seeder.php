<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PermissionV2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert(['name' => 'big_course-index', 'guard_name' => 'api', 'category' => '课程管理', 'title' => '大课程管理', 'description' => '大课程管理', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        DB::table('permissions')->insert(['name' => 'official_account-index|type[art]', 'guard_name' => 'api', 'category' => '公众号管理', 'title' => '查看艺术编程消息推送', 'description' => '查看艺术编程消息推送', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'official_account-index|type[music]', 'guard_name' => 'api', 'category' => '公众号管理', 'title' => '查看数字音乐消息推送', 'description' => '查看数字音乐消息推送', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        DB::table('permissions')->insert(['name' => 'official_account-store:category[1]', 'guard_name' => 'api', 'category' => '公众号管理', 'title' => '新增艺术编程消息推送', 'description' => '新增艺术编程消息推送', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('permissions')->insert(['name' => 'official_account-store:category[2]', 'guard_name' => 'api', 'category' => '公众号管理', 'title' => '新增数字音乐消息推送', 'description' => '新增数字音乐消息推送', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
    }
}
