<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Telescope\Telescope;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Token;

// use Illuminate\Support\Facades\Cookie;

class TelescopeAuthorize extends BaseMiddleware
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
        // todo Test!
        // Cookie::queue(
        //     'token',
        //     'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9kZXYucGxhdGZvcm0uZG4uY29tXC9hcGlcL2F1dGhcL2xvZ2luIiwiaWF0IjoxNTQ1MzgxNjU3LCJleHAiOjE5MDUzODE2NTcsIm5iZiI6MTU0NTM4MTY1NywianRpIjoidjZvYVp3dE5mdDVISlJpWCIsInN1YiI6MTIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.gLhUlCobZF82jsdGmessi_GheJbFUknOjnUss1Yy3a0',
        //     100
        // );
        if ($request->hasCookie('token') && app()->environment() === 'production') {
            try {
                $this->auth->setToken(new Token($request->cookie('token')))->authenticate();
            } catch (TokenInvalidException $exception) {
                abort(403);
            }
        }

        return Telescope::check($request) ? $next($request) : abort(403);
    }
}
