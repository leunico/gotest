<?php

namespace Modules\Examinee\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard;
use Modules\Examinee\Http\Controllers\ExamineeController as Controller;
use Modules\Examinee\Http\Requests\ExamineeLoginRequest;
use Modules\Examinee\Transformers\ExamineeResource;
use Modules\Examinee\Http\Requests\ExamineeResetPasswordRequest;
use Illuminate\Support\Facades\Hash;
use Modules\Examinee\Http\Requests\ExamineePasswordRequest;

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
        return Auth::guard('examinee');
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param \Modules\Examinee\Http\Requests\ExamineeLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function login(ExamineeLoginRequest $request): JsonResponse
    {
        $credentials = [
            'certificates' => $request->certificates,
            'password' => $request->input('password', ''),
        ];

        if ($token = $this->guard()->attempt($credentials)) {
            $user = $this->guard()->user();
            if (empty($user->status)) {
                return $this->response()->errorForbidden('你的账号暂时无效。');
            }

            return $this->respondWithToken($token);
        }

        return $this->response()->errorUnprocessableEntity('您的密码不正确！');
    }

    /**
     * reset password.
     *
     * @param \Modules\Examinee\Http\Requests\ExamineeResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function resetPassword(ExamineeResetPasswordRequest $request): JsonResponse
    {
        if ($request->password) {
            $request->examinee->password = Hash::make($request->password);
            if ($request->examinee->save()) {
                return $this->response()->success(['message' => 'Successfully reset password']);
            } else {
                return $this->response()->error();
            }
        }

        return $this->response()->success();
    }

    /**
     * update password.
     *
     * @param \Modules\Examinee\Http\Requests\ExamineePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function updatePassword(ExamineePasswordRequest $request): JsonResponse
    {
        $examinee = $this->examinee();
        $examinee->password = Hash::make($request->new_password);

        return $this->response()->success($examinee->save());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function userInfo(): JsonResponse
    {
        return $this->response()->item($this->guard()->user(), ExamineeResource::class);
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
}
