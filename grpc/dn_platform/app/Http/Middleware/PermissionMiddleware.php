<?php

namespace App\Http\Middleware;

use Closure;
use Spatie\Permission\Exceptions\UnauthorizedException;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  array $role
     * @return mixed
     */
    public function handle($request, Closure $next, ...$permission)
    {
        if (auth('api')->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        // todo 超级权限者
        if ($request->user()->isSuperAdmin()) {
            return $next($request);
        }

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        foreach ($permissions as $permission) {
            if (str_contains($permission, ':')) {
                $result = explode(':', $permission);
                if (! $request->user()->can($result[0]) &&
                    ! $request->user()->can($permission . '['. $request->input($result[1], null) .']')) {
                    throw UnauthorizedException::forPermissions($permissions);
                }
            } elseif (str_contains($permission, '|')) {
                $result = explode('|', $permission);
                if (! $request->user()->can($result[0]) &&
                    ! $request->user()->can($permission . '['. $request->route($result[1], null) .']')) {
                    throw UnauthorizedException::forPermissions($permissions);
                }
            } else {
                if (! $request->user()->can($permission)) {
                    throw UnauthorizedException::forPermissions($permissions);
                }
            }
        }

        return $next($request);
    }
}
