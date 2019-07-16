<?php

namespace Modules\Examinee\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ExamineeAnswerSimulationMiddleware
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
        if (! empty($request->simulation)) {
            return response()->json(true);
        }

        return $next($request);
    }
}
