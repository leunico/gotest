<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRolePost;
use App\Role;
use function App\responseSuccess;
use function App\responseFailed;
use App\Rules\ArrayExists;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\User;

class RoleController extends Controller
{
    /**
     * 角色列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Role $role
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, Role $role)
    {
        $keyword = $request->input('keyword', null);

        $data = $role->with(['creator'])
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('title', 'like', "%$keyword%");
            })
            ->select('id', 'title', 'description', 'creator_id', 'created_at', 'name')
            ->get();

        return responseSuccess($data);
    }

    /**
     * 创建角色.
     *
     * @param \App\Http\Requests\StoreRolePost $request
     * @param \App\Role $role
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreRolePost $request, Role $role)
    {
        $role->name = $request->name;
        $role->title = $request->title;
        $role->creator_id = $request->user()->id;
        $role->description = $request->input('description', '');

        if ($role->save()) {
            return responseSuccess([
                'role_id' => $role->id
            ], '创建角色成功');
        } else {
            return responseFailed('创建角色失败', 500);
        }
    }

    /**
     * 角色设置权限
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function permission(Request $request, Role $role)
    {
        $this->validate($request, ['permissions' => [
            'array',
            new ArrayExists(new Permission)
        ]]);

        if ($role->syncPermissions($request->permissions)) {
            return responseSuccess([
                'role_id' => $role->id
            ], '同步权限成功');
        } else {
            return responseFailed('同步权限失败', 500);
        }
    }

    /**
     * 获取角色
     *
     * @param \App\Role $role
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(Role $role)
    {
        $role->load([
            'permissions' => function ($query) {
                $query->select('id', 'name', 'category');
            }
        ]);

        return responseSuccess($role);
    }

    /**
     * 更新角色.
     *
     * @param \App\Http\Requests\StoreRolePost $request
     * @param \App\Role $role
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreRolePost $request, Role $role)
    {
        if ($role->name == 'admin') {
            return responseFailed('不可修改角色', 422);
        }

        $role->name = $request->name;
        $role->title = $request->title;
        $role->description = $request->input('description', '');

        if ($role->save()) {
            return responseSuccess([
                'role_id' => $role->id
            ], '更新角色成功');
        } else {
            return responseFailed('更新角色失败', 500);
        }
    }

    /**
     * 删除
     *
     * @param \App\Role $role
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function destroy(Role $role)
    {
        if (User::role($role->name)->get()->isNotEmpty()) {
            return responseFailed('该角色已有用户', 403);
        }

        $role->delete();

        return responseSuccess();
    }
}
