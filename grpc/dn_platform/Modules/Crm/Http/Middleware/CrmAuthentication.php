<?php

namespace Modules\Crm\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CrmAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            //  base 64 {id: 'id', timestamp: 'timestamp'}
            $authorization = $request->get('authorization');
            //  md5 $id.$secret.$timestamp
            $sig = $request->get('sig');

            $json = base64_decode($authorization);
            $authData = json_decode($json, true);
            if (is_array($authData)) {
                $apiAuth = config('services.auth');
                $authId = $apiAuth['platform_id'];
                $authSecret = $apiAuth['platform_secret'];
                if (empty($authData['timestamp'])) {
                    throw new \Exception('请求参数不正确！');
                }
                $secret = $authId . $authSecret . $authData['timestamp'];
                $sigStr = md5($secret);
                if ($sig === $sigStr) {
                    return $next($request);
                }
            }
            throw new \Exception('非法请求');
        } catch (\Exception $e) {
            $msg = empty($e->getMessage()) ? '禁止访问' : $e->getMessage();
            abort(403, $msg);
        }
    }
}
