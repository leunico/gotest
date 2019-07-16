<?php

namespace App\Http\Middleware;

use Closure;
use Spatie\Permission\Exceptions\UnauthorizedException;

class PremissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure  $next
     * @param  array $role
     * @return mixed
     */
    public function handle($request, Closure $next, ...$permission)
    {
        if (auth('api')->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        // 超级管理员
        if ($request->user()->isSuperAdmin()) {
            return $next($request);
        }

        $permissions = is_array($permission)
            ? $permission
            : explode(',', $permission);

        foreach ($permissions as $permission) {
            if (str_contains($permission, ':')) { // 带在参数上的
                $result = explode(':', $permission);
                if ($request->user()->can($result[0]) ||
                    $request->user()->can($permission . '['. $request->input($result[1], null) .']')) {
                    return $next($request);
                }
            } elseif (str_contains($permission, '|')) { // 带在路由上的
                $result = explode('|', $permission);
                if ($request->user()->can($result[0]) ||
                    $request->user()->can($permission . '['. $request->route($result[1], null) .']')) {
                    return $next($request);
                }
            } else {
                if ($request->user()->can($permission)) {
                    return $next($request);
                }
            }
        }

        throw UnauthorizedException::forPermissions($permissions);
    }
}
