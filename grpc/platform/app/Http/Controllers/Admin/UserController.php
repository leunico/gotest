<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Rules\ArrayExists;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * 用户列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, User $user): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $isAll = (int) $request->input('is_all', null);

        $keyword = $request->input('keyword', null);
        $role = $request->input('role', null);
        $status = $request->input('status', null);
        $category = $request->input('category', null);
        $startTime = $request->input('start_time', 0);
        $endTime = $request->input('end_time', 0);

        $query = $user->select('id', 'name', 'real_name', 'phone', 'email', 'created_at', 'remarks', 'category', 'account_status')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%")
                        ->orWhere('phone', 'like', "%$keyword%")
                        ->orWhere('email', 'like', "%$keyword%");
                });
            })
            ->when($role, function ($query) use ($role) {
                $query->role($role);
            })
            ->when($category, function ($query) use ($category) {
                $query->where('category', 'Like', "%$category%");
            })
            ->when(! is_null($status), function ($query) use ($status) {
                $query->where('account_status', $status);
            })
            ->when((! empty($startTime) || ! empty($endTime)), function ($query) use ($startTime, $endTime) {
                $query->whereBetween('created_at', [$startTime, (empty($endTime) ? Carbon::now() : Carbon::parse($endTime))->endOfday()]);
            })
            ->orderBy('id', 'desc')
            ->with(['roles:id,name,title']);

        return $this->response()->success(empty($isAll) ? $query->paginate($perPage) : $query->get());
    }

    /**
     * 创建用户.
     *
     * @param \App\Models\User $user
     * @param \App\Http\Requests\StoreUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreUserRequest $request, User $user): JsonResponse
    {
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->real_name = $request->real_name;
        $user->creator_id = $this->user()->id;
        $user->sex = $request->input('sex', 0);
        $user->remarks = $request->input('remarks', '');
        $user->category = $request->input('category', []);
        $user->password = Hash::make($request->password);
        $user->getConnection()->transaction(function () use ($user, $request) {
            if ($user->save()) {
                $user->assignRole($request->role);
            }
        });

        return $this->response()->success($user);
    }

    /**
     * 获取用户.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(User $user): JsonResponse
    {
        $user->load([
            'roles:id,name,title'
        ]);

        $user->getAllPermissions();

        return $this->response()->success($user);
    }

    /**
     * 编辑用户.
     *
     * @param \App\Http\Requests\StoreUserRequest $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreUserRequest $request, User $user): JsonResponse
    {
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->real_name = $request->real_name;
        $user->remarks = $request->input('remarks', '');
        $user->sex = $request->input('sex', 0);
        $user->category = $request->input('category', []);
        $user->password = $request->password ? Hash::make($request->password) : $user->password;
        $user->getConnection()->transaction(function () use ($user, $request) {
            if ($user->save()) {
                $user->syncRoles([$request->role]);
            }
        });

        return $this->response()->success($user);
    }

    /**
     * 设置账号状态
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function setStatus(User $user): JsonResponse
    {
        $user->account_status = ! $user->account_status;

        return $user->save() ? $this->response()->success($user) : $this->response()->error();
    }

    /**
     * 设置账号角色
     *
     * @param \App\Models\User $user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function setRole(Request $request, User $user): JsonResponse
    {
        $this->validate($request, [
            'role' => [
                'exists:roles,name',
            ],
        ]);

        $user->syncRoles($request->input('role', []));

        return $this->response()->success();
    }

    /**
     * 获取账号权限
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function permissions(User $user): JsonResponse
    {
        return $this->response()->success($user->getAllPermissions());
    }

    /**
     * 设置账号权限
     *
     * @param \App\Models\User $user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function setPremissions(Request $request, User $user): JsonResponse
    {
        $this->validate($request, [
            'permissions' => [
                'array',
                new ArrayExists(new Permission(), false, false, 'name'),
            ],
        ]);

        $user->syncPermissions($request->permissions);

        return $this->response()->success();
    }
}
