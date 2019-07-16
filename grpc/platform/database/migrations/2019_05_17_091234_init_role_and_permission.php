<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class InitRoleAndPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $roles = [
            [
                'name' => 'exam_organization',
                'guard_name' => 'api',
                'title' => '考组管理员',
                'description' => '考组管理员'
            ],
            [
                'name' => 'exam_examination_paper',
                'guard_name' => 'api',
                'title' => '教研老师',
                'description' => '考卷管理员'
            ],
            [
                'name' => 'exam_examinee',
                'guard_name' => 'api',
                'title' => '教务老师',
                'description' => '考生管理员'
            ],
            [
                'name' => 'exam_marking',
                'guard_name' => 'api',
                'title' => '阅卷老师',
                'description' => '阅卷管理员'
            ],
            [
                'name' => 'exam_technical',
                'guard_name' => 'api',
                'title' => '技术支持',
                'description' => '技术支持管理'
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        $permissions = [
            // 考试管理
            [
                'name' => 'exam_match_menu',
                'guard_name' => 'api',
                'title' => '考试赛事查看',
                'category' => '考试管理',
                'description' => ''
            ],
            [
                'name' => 'exam_match_edit',
                'guard_name' => 'api',
                'title' => '考试赛事增删查改',
                'category' => '考试管理',
                'description' => ''
            ],
            [
                'name' => 'examination_list',
                'guard_name' => 'api',
                'title' => '考试管理查看',
                'category' => '考试管理',
                'description' => ''
            ],
            [
                'name' => 'examination_edit',
                'guard_name' => 'api',
                'title' => '考试管理增删查改',
                'category' => '考试管理',
                'description' => ''
            ],

            // 教研老师（试卷管理）
            [
                'name' => 'examination_paper_list',
                'guard_name' => 'api',
                'title' => '考卷管理查看',
                'category' => '考卷管理',
                'description' => ''
            ],
            [
                'name' => 'examination_paper_edit',
                'guard_name' => 'api',
                'title' => '考卷管理增删查改',
                'category' => '考卷管理',
                'description' => ''
            ],

            // 教务老师（考生管理）
            [
                'name' => 'examinee_edit',
                'guard_name' => 'api',
                'title' => '考生管理增删查改',
                'category' => '考生管理',
                'description' => ''
            ],
            [
                'name' => 'examinee_list',
                'guard_name' => 'api',
                'title' => '考生管理查看',
                'category' => '考生管理',
                'description' => ''
            ],

            [
                'name' => 'score_sheet_list',
                'guard_name' => 'api',
                'title' => '成绩管理查看',
                'category' => '成绩管理',
                'description' => ''
            ],
            [
                'name' => 'score_sheet_edit',
                'guard_name' => 'api',
                'title' => '成绩管理增删查改',
                'category' => '成绩管理',
                'description' => ''
            ],

            [
                'name' => 'examination_cheat_list',
                'guard_name' => 'api',
                'title' => '防作弊管理查看',
                'category' => '防作弊管理',
                'description' => ''
            ],
            [
                'name' => 'examination_cheat_edit',
                'guard_name' => 'api',
                'title' => '防作弊管理增删查改',
                'category' => '防作弊管理',
                'description' => ''
            ],

            // 阅卷管理
            [
                'name' => 'examination_marking_edit',
                'guard_name' => 'api',
                'title' => '阅卷管理阅卷权限',
                'category' => '阅卷管理',
                'description' => ''
            ],
            [
                'name' => 'examination_marking_list',
                'guard_name' => 'api',
                'title' => '阅卷管理查看',
                'category' => '阅卷管理',
                'description' => ''
            ],

            // 技术支持
            [
                'name' => 'technical_support_list',
                'guard_name' => 'api',
                'title' => '技术请求查看',
                'category' => '技术请求',
                'description' => ''
            ],
            [
                'name' => 'technical_support_edit',
                'guard_name' => 'api',
                'title' => '技术请求删查改',
                'category' => '技术请求',
                'description' => ''
            ],

            // 员工管理
            [
                'name' => 'staff_manage_list',
                'guard_name' => 'api',
                'title' => '考组人员查看',
                'category' => '员工管理',
                'description' => ''
            ],
            [
                'name' => 'staff_manage_edit',
                'guard_name' => 'api',
                'title' => '考组人员增删查改',
                'category' => '员工管理',
                'description' => ''
            ],
            [
                'name' => 'staff_manage_role_permission',
                'guard_name' => 'api',
                'title' => '权限管理查看',
                'category' => '员工管理',
                'description' => ''
            ],
            [
                'name' => 'staff_manage_permission_edit',
                'guard_name' => 'api',
                'title' => '权限管理增删查看',
                'category' => '员工管理',
                'description' => ''
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
