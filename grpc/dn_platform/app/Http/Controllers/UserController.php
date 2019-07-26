<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserPost;
use Modules\Personal\Events\ChangeUser;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\JsonResponse;
use App\User;
use function App\responseFailed;
use App\UserAddress;
use App\Http\Resources\UserResource;
use Modules\Operate\Entities\WechatUser;
use App\Http\Resources\UserTeacherResource;

class UserController extends Controller
{
    /**
     * 创建用户.
     *
     * @param \App\User $user
     * @param \App\Http\Requests\StoreUserPost $request
     * @param \Tymon\JWTAuth\JWTAuth $auth
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreUserPost $request, JWTAuth $auth, User $user): JsonResponse
    {
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->name = $request->name;
        $password = $request->input('password', 123456); // todo 默认的

        if ($password !== null) {
            $user->createPassword($password);
        }

        if (! $user->save()) {
            return $this->response()->error('注册失败');
        }

        event(new ChangeUser($user, 'create'));
        return $this->respondWithToken($auth->fromUser($user));
    }

    /**
     * 更新用户.
     *
     * @param \App\Http\Requests\StoreUserPost $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreUserPost $request): JsonResponse
    {
        $user = $request->user();
        $user->name = $request->name ?? $user->name;
        $user->avatar = $request->avatar ?? $user->avatar;
        $user->real_name = $request->real_name ?? $user->real_name;
        $user->grade = $request->grade ?? $user->grade;
        $user->phone = $request->phone ?? $user->phone;
        $user->sex = $request->sex ?? $user->sex;

        $user->getConnection()->transaction(function () use ($request, $user) {
            if ($user->save()) {
                $address = UserAddress::firstOrNew(['user_id' => $user->id]);
                $address->province_id = $request->province_id ?? $address->province_id;
                $address->city_id = $request->city_id ?? $address->city_id;
                $address->district_id = $request->district_id ?? $address->district_id;
                $address->receiver = $request->receiver ?? $address->receiver;
                $address->detail = $request->address_detail ?? $address->address_detail;
                $address->save();

                event(new ChangeUser($user, 'update'));
            }
        });

        return $this->response()->item($user, UserResource::class);
    }

    /**
     * 用户钱包.
     *
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function wallet(): JsonResponse
    {
        $wallet = [
            'star_amount' => $this->user()->star_amount,
            'star_package_all' => $this->user()->starPackgeUsers->sum('star')
        ];

        return $this->response()->success($wallet);
    }
}
