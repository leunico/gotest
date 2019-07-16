<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\JsonResponse;
use App\Rules\ArrayExists;

class RoleController extends Controller
{
    /**
     * 角色列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Spatie\Permission\Models\Role $role
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, Role $role): JsonResponse
    {
        $keyword = $request->input('keyword', null);

        $data = $role->select('id', 'title', 'description', 'creator_id', 'created_at', 'name')
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('title', 'like', "%$keyword%");
            })
            ->with(['permissions'])
            ->get();

        return $this->response()->success($data);
    }

    /**
     * 创建角色.
     *
     * @param \App\Http\Requests\StoreRolePost $request
     * @param \Spatie\Permission\Models\Role $role
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreRoleRequest $request, Role $role): JsonResponse
    {
        $role->name = $request->name;
        $role->title = $request->title;
        $role->creator_id = $this->user()->id;
        $role->description = $request->input('description', '');

        return $role->save() ? $this->response()->success() : $this->response()->error();
    }

    /**
     * 角色设置权限
     *
     * @param \Illuminate\Http\Request $request
     * @param \Spatie\Permission\Models\Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function permission(Request $request, Role $role): JsonResponse
    {
        $this->validate($request, ['permissions' => [
            'array',
            new ArrayExists(new Permission)
        ]]);

        return $role->syncPermissions($request->permissions) ? $this->response()->success() : $this->response()->error();
    }

    /**
     * 获取角色
     *
     * @param \Spatie\Permission\Models\Role $role
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(Role $role): JsonResponse
    {
        $role->load([
            'permissions' => function ($query) {
                $query->select('id', 'name', 'category');
            }
        ]);

        return $this->response()->success($role);
    }

    /**
     * 更新角色.
     *
     * @param \App\Http\Requests\StoreRolePost $request
     * @param \App\Role $role
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreRoleRequest $request, Role $role): JsonResponse
    {
        if (empty($role->creator_id)) {
            return $this->response()->errorUnprocessableEntity('系统角色，不可修改！');
        }

        $role->name = $request->name;
        $role->title = $request->title;
        $role->description = $request->input('description', '');

        return $role->save() ? $this->response()->success() : $this->response()->error();
    }

    /**
     * 删除
     *
     * @param \App\Role $role
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function destroy(Role $role): JsonResponse
    {
        if (User::role($role->name)->get()->isNotEmpty()) {
            return $this->response()->errorForbidden('该角色已有用户');
        }

        $role->delete();

        return $this->response()->success();
    }
}
