<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard;
use function App\username;
use App\User;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Modules\Operate\Entities\WechatUser;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Event;
use App\Events\Login;
use EasyWeChat\MiniProgram\Application;
use EasyWeChat\Kernel\Exceptions\DecryptException;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUserPost;

class AuthController extends Controller
{
    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     * @author lizx
     */
    public function guard(): Guard
    {
        return Auth::guard('api');
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function login(Request $request): JsonResponse
    {
        $login = (string) $request->input('login', '');
        $field = username($login);

        if (in_array($field, ['phone', 'email'])) {
            if (! $user = User::where($field, $login)->first()) {
                return $this->response()->errorUnprocessableEntity(sprintf('%s还没有注册', $field == 'phone' ? '手机号' : '邮箱'));
            }
        }

        $credentials = [
            $field => $login,
            'password' => $request->input('password', ''),
        ];

        if ($token = $this->guard()->attempt($credentials)) {
            $user = $this->guard()->user();
            if (empty($user->account_status)) {
                return $this->response()->errorForbidden('你在小黑屋哦');
            }

            // 登录成功触发事件
            // Event::fire(new Login($user));
            return $this->respondWithToken($token);
        }

        return $this->response()->errorUnprocessableEntity('账号或密码不正确');
    }

    /**
     * 用户第一次登陆[微信].
     *
     * @param \App\Http\Requests\StoreUserPost $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function loginWechat(StoreUserPost $request): JsonResponse
    {
        if ($token = $this->guard()->attempt([
                'phone' => $request->phone,
                'password' => $request->password
            ])) {
            $user = $this->guard()->user();
            if (empty($user->account_status)) {
                return $this->response()->errorForbidden('你在小黑屋哦');
            }

            if (empty($user->unionid)) {
                $user->unionid = $request->unionid;
                if (! $user->save() || ! $user->wechatUser) {
                    return $this->response()->errorServer('用户绑定失败');
                }
            } else {
                return $this->response()->errorForbidden('用户不匹配');
            }

            return $this->respondWithToken($token);
        }

        return $this->response()->errorUnprocessableEntity('账号或密码不正确');
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function me(): JsonResponse
    {
        return $this->response()->item(auth('api')->user(), UserResource::class);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function logout(): JsonResponse
    {
        $this->guard()->logout();

        return $this->response()->success(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     * 刷新token，如果开启黑名单，以前的token便会失效。
     *
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * 小程序登陆
     *
     * @param \Illuminate\Http\Request $request
     * @param \EasyWeChat\MiniProgram\Application $miniProgram
     * @return \Illuminate\Http\JsonResponse
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     * @throws \Exception
     * @author lizx
     */
    public function miniProgramLogin(Request $request, Application $miniProgram): JsonResponse
    {
        $this->validate($request, [
            'code' => 'required|string',
            'encryptData' => 'required|string',
            'iv' => 'required|string',
        ]);

        $auths = $miniProgram->auth->session($request->code);
        if (isset($auths['errcode'])) {
            return $this->response()->errorUnprocessableEntity($auths);
        }

        try {
            $decryptedData = $miniProgram->encryptor->decryptData($auths['session_key'], $request->iv, $request->encryptData);
            if (! isset($decryptedData['unionId']) || empty($decryptedData['unionId'])) {
                return $this->response()->error('Decrypt Error.', 500);
            }

            $wechatUser = WechatUser::firstOrNew(['unionid' => $decryptedData['unionId']]);
            if (($user = User::where('unionid', $decryptedData['unionId'])->first()) && $wechatUser) {
                if (empty($wechatUser->mini_program_openid)) {
                    $wechatUser->mini_program_openid = $decryptedData['openId'];
                    $wechatUser->save();
                }

                if (empty($user->account_status)) {
                    return $this->response()->errorForbidden('你在小黑屋哦');
                }

                return $this->respondWithToken($this->guard()->login($user));
            } else {
                $wechatUser->nickname = $decryptedData['nickName'];
                $wechatUser->sex = $decryptedData['gender'];
                $wechatUser->language = $decryptedData['language'];
                $wechatUser->city = $decryptedData['city'];
                $wechatUser->province = $decryptedData['province'];
                $wechatUser->country = $decryptedData['country'];
                $wechatUser->headimgurl = $decryptedData['avatarUrl'];
                $wechatUser->mini_program_openid = $decryptedData['openId'];
                $wechatUser->save();
            }

            return $this->response()->success(['unionId' => $decryptedData['unionId']]);
        } catch (DecryptException $exception) {
            // \Log::error('小程序登陆解密失败：' . $exception->getTraceAsString());
            return $this->response()->errorServer('Decrypt Error.' . $exception->getMessage());
        } catch (\Exception $exception) {
            // dd($exception->getTraceAsString());
            return $this->response()->errorServer('Service Error.' . $exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $driver
     * @return JsonResponse
     */
    public function socialiteUser(Request $request, $driver): JsonResponse
    {
        $code = $request->code;
        if (empty($code)) {
            return $this->response()->errorUnprocessableEntity('code必须填写');
        }

        $socialiteDriver = Socialite::driver($driver);

        try {
            $response = $socialiteDriver->getAccessTokenResponse($code);
            /**
             *
            [
            "access_token" => "15_jSbnPtH21GABfmmLRfRpvb4RGuueFzSvY9ODyQ8_7gY-Kyv-ggFE9WmsNcVz_GvUK2vJhzUlrliQWoKoSvr6yw",
            "expires_in" => 7200,
            "refresh_token" => "15_Eoe22KpIp5uKvSgQODNMIWVE3yMejbSpNF9-h-JFZXmKXMQg7Yq1py94ApD5Ts_Lue9xcK3KIosuWWy3IUEFPQ",
            "openid" => "o6MUTwtACE8qDHyZOfeUV2VsRIVc",
            "scope" => "snsapi_login",
            "unionid" => "ovPdKs0ADUkX7XIz6gxUJtrdcyyU",
            ]
             */
            DB::beginTransaction();

            if (!empty($response['unionid'])) {
                $wechatUser = WechatUser::where('unionid', $response['unionid'])->first();
                $unionid = $response['unionid'];
            } else {
                $socialiteDriver->setOpenId($response['openid']);
                $oauthUser = $socialiteDriver->userFromToken($response['access_token']);

                //todo 测试环境怎么处理？
                $unionid = !empty($oauthUser->user['unionid']) ? $oauthUser->user['unionid'] : null;

                $wechatUser = WechatUser::where('unionid', $unionid)->first();

                if (empty($wechatUser)) {
                    $wechatUser = new WechatUser();
                    $wechatUser->unionid = $unionid;
                    $wechatUser->nickname = $oauthUser->nickname;
                    $wechatUser->sex = $oauthUser->user['sex'];
                    $wechatUser->language = $oauthUser->user['language'];
                    $wechatUser->city = $oauthUser->user['city'];
                    $wechatUser->province = $oauthUser->user['province'];
                    $wechatUser->country = $oauthUser->user['country'];
                    $wechatUser->headimgurl = $oauthUser->user['headimgurl'];

                    //todo 其他方式的登录
                    if ($driver == 'weixinweb') {
                        $wechatUser->website_openid = $oauthUser->id;
                    }
                    $wechatUser->save();
                }
            }

            $user = $wechatUser->user;
            if (empty($user)) {
                $user = new User();
                $user->name = $wechatUser->nickname;
                $user->sex = $wechatUser->sex;
                $user->unionid = $unionid;
                $user->password = bcrypt('123456');
                //todo 头像
                $user->save();
            }

            DB::commit();

            $token = JWTAuth::fromUser($user);

            return $this->respondWithToken($token);
        } catch (\ErrorException $exception) {
            \Log::error('获取access_token失败：' . $exception->getTraceAsString());
            DB::rollBack();

            return $this->response()->error('不合法的Code', 422);
        } catch (\Exception $exception) {
            \Log::error($exception->getTraceAsString());
            DB::rollBack();

            return $this->response()->error('', 500);
        }
    }
}
