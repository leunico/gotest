<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function userInfo(): JsonResponse
    {
        return $this->response()->item($this->user(), UserResource::class);
    }

    /**
     * 用户更新.
     *
     * @param \App\Http\Requests\StoreUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreUserRequest $request): JsonResponse
    {
        $user = $this->user();
        $user->avatar = $request->avatar;
        $user->real_name = $request->real_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->remarks = $request->remarks ?? $user->remarks;
        $user->sex = $request->sex ?? $user->sex;
        $user->password = $request->password ? Hash::make($request->password) : $user->password;

        return $user->save() ? $this->response()->item($user, UserResource::class) : $this->response()->error();
    }
}
