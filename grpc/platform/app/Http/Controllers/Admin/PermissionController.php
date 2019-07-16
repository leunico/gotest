<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\StorePermissionRequest;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    /**
     * 权限列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $isAll = $request->input('is_all', null);

        $data = $isAll ? Permission::all() : Permission::paginate($perPage);

        return $this->response()->success($data->groupBy('category'));
    }

    /**
     * 创建权限.
     *
     * @param \App\Http\Requests\StorePermissionRequest $request
     * @param \Spatie\Permission\Models\Permission $permission
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StorePermissionRequest $request, Permission $permission): JsonResponse
    {
        $permission->name = $request->name;
        $permission->title = $request->title;
        $permission->category = $request->category;
        $permission->description = $request->input('description', '');

        return $permission->save() ? $this->response()->success() : $this->response()->error();
    }

    /**
     * 获取权限
     *
     * @param \Spatie\Permission\Models\Permission $permission
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(Permission $permission): JsonResponse
    {
        return $this->response()->success($permission);
    }

    /**
     * 修改权限.
     *
     * @param \App\Http\Requests\StorePermissionRequest $request
     * @param \Spatie\Permission\Models\Permission $permission
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StorePermissionRequest $request, Permission $permission): JsonResponse
    {
        $permission->title = $request->title;
        $permission->category = $request->category;
        $permission->description = $request->input('description', '');

        return $permission->save() ? $this->response()->success() : $this->response()->error();
    }
}
