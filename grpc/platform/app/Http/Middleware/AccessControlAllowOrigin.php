<?php

namespace App\Http\Middleware;

use Closure;

class AccessControlAllowOrigin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $origin = $request->header('Origin');
        $domains = config('services.allow_origin_urls') ? explode(',', config('services.allow_origin_urls')) : [];

        if (config('services.allow_any_cors') || in_array($origin, $domains)) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE, PATCH");
            header("Access-Control-Allow-Headers: Content-Type,Access-Token,X-XSRF-TOKEN,Authorization");
            header("Access-Control-Expose-Headers: *");
        }

        return $next($request);
    }
}
