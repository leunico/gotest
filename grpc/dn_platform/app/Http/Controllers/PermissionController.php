<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StorePermissionPost;
use Spatie\Permission\Models\Permission;
use function App\responseSuccess;
use function App\responseFailed;

class PermissionController extends Controller
{
    /**
     * 权限列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);
        $isAll = $request->input('is_all', null);

        $data = $isAll ? Permission::all() : Permission::paginate($perPage);

        return responseSuccess($data->groupBy('category'));
    }

    /**
     * 创建权限.
     *
     * @param \App\Http\Requests\StorePermissionPost $request
     * @param \Spatie\Permission\Models\Permission $permission
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StorePermissionPost $request, Permission $permission)
    {
        $permission->name = $request->name;
        $permission->title = $request->title;
        $permission->category = $request->category;
        $permission->description = $request->input('description', '');

        if ($permission->save()) {
            return responseSuccess([
                'permission_id' => $permission->id
            ], '创建权限成功');
        } else {
            return responseFailed('创建权限失败', 500);
        }
    }
}
