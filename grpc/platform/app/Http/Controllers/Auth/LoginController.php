<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use function App\username;
use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function aLogin(Request $request): JsonResponse
    {
        $login = (string) $request->input('login', '');
        $field = username($login);

        if (in_array($field, ['phone', 'email'])) {
            if (! User::where($field, $login)->first()) {
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
                return $this->response()->errorForbidden('你在小黑屋哦~');
            }

            return $this->respondWithToken($token);
        }

        return $this->response()->errorUnprocessableEntity('账号或密码不正确');
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
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('api');
    }
}
