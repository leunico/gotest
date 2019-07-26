<?php

namespace Modules\Crm\Http\Controllers\Api;

use function App\errorLog;
use function App\responseFailed;
use function App\responseSuccess;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class UsersController
 * @package Modules\Crm\Http\Controllers\Api
 */
class UsersController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrCreate(Request $request)
    {
        try {

            $mobile = $request->mobile;
            $name   = $request->name;

            $mobileArr = explode(',', $mobile);

            //查找对应的用户
            $existsUsers = User::query()->whereIn('phone', $mobileArr)->select('id', 'name', 'real_name',
                'phone')->get();

            $existMobiles = $existsUsers->pluck('phone')->toArray();

            $notExistMobiles = array_diff($mobileArr, $existMobiles);

            $channelId = $request->get('channel_id', 0);

            foreach ($notExistMobiles as $notExistMobile) {
                $user             = new User();
                $user->name       = User::createName($name);
                $user->phone      = $notExistMobile;
                $user->createPassword(substr($notExistMobile, 5));
                $user->real_name  = $request->name ?? '';
                $user->age        = $request->age ?? 0;
                $user->grade      = $request->grade ?? 0;
                $user->channel_id = $channelId;
                $user->save();

                $existsUsers->push($user);
            }
            return responseSuccess($existsUsers);
        } catch (\Exception $e) {
            errorLog($e);
            return responseFailed($e->getMessage());
        }
    }

    /**
     * user_ids mobile 多个用,分割
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers(Request $request)
    {
        try {
            if ($request->has('user_ids') && $request->user_ids) {
                $user_ids = explode(',', $request->user_ids);

                if ($request->get('format') == 'collection' || count($user_ids) > 1) {
                    $users = User::query()->whereIn('id', $user_ids)
                        ->select('id', 'name', 'real_name', 'phone', 'age', 'sex')
                        ->get();
                    return responseSuccess($users, '');
                } else {
                    if ($user = User::query()->where('id', $user_ids[0])->select('id', 'name', 'real_name', 'phone',
                        'age',
                        'sex')->first()) {
                        return responseSuccess($user, '');
                    }
                }
            }

            if ($request->has('mobile') && $request->mobile) {
                $mobile = explode(',', $request->mobile);

                if ($request->get('format') == 'collection' || count($mobile) > 1) {
                    $users = User::query()->whereIn('phone', $mobile)
                        ->select('id', 'name', 'real_name', 'phone')
                        ->get();
                    return responseSuccess($users, '');
                } else {
                    $user = User::query()->where('phone', $mobile[0])->select('id', 'name', 'real_name',
                        'phone')->first();
                    if ($user) {
                        return responseSuccess($user, '');
                    }
                }
            }
            return responseFailed('无数据');
        } catch (\Exception $e) {
            errorLog($e);
            return responseFailed($e->getMessage());
        }
    }
}
