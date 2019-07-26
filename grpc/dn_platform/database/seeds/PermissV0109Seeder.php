<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PermissV0109Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            'name' => 'see-news-article',
            'guard_name' => 'api',
            'category' => '新闻资讯文章管理',
            'title' => '查看官网文章',
            'description' => '查看官网文章',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'release-news-article',
            'guard_name' => 'api',
            'category' => '新闻资讯文章管理',
            'title' => '发布官网文章',
            'description' => '发布官网文章',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'add-news-article',
            'guard_name' => 'api',
            'category' => '新闻资讯文章管理',
            'title' => '新增官网文章',
            'description' => '新增官网文章',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('permissions')->insert([
            'name' => 'delete-news-article',
            'guard_name' => 'api',
            'category' => '新闻资讯文章管理',
            'title' => '删除官网文章',
            'description' => '删除官网文章',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
