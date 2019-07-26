<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use App\Exceptions\ValidationException as ValidationJsonException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\Helpers;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class Handler extends ExceptionHandler
{
    use Helpers;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'message' => '请先登录',
                'redirect_url' => config('services.login_redirect_url')
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'message' => '您没有权限',
                'redirect_url' => 'JavaScript:history.go(-1)'
            ], Response::HTTP_FORBIDDEN);
        }

        if ($exception instanceof ThrottleRequestsException) {
            return response()->json([
                'message' => '点击过于频繁'
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return $this->invalidJson($request, $exception);
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($exception instanceof ModelNotFoundException) {
            $exception = new NotFoundHttpException($exception->getMessage(), $exception); // todo 直接借用404就行吧？
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Validation\ValidationException $exception
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    protected function invalidJson($request, \Illuminate\Validation\ValidationException $exception)
    {
        return parent::invalidJson($request, new ValidationJsonException($exception));
    }
}
