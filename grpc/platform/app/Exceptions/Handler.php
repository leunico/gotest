<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use App\Exceptions\ValidationException as ValidationJsonException;
use Illuminate\Http\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Examinee\Exceptions\ExcelImportExamineeException;
use Illuminate\Auth\Access\AuthorizationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
                'message' => '请先登录'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof ThrottleRequestsException) {
            return response()->json([
                'message' => '点击过于频繁'
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        if ($exception instanceof UnauthorizedException) {
            return response()->json([
                'message' => '权限错误：' . $exception->getMessage()
            ], Response::HTTP_FORBIDDEN);
        }

        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'message' => '你没有这个操作的权限：' . $exception->getMessage()
            ], Response::HTTP_FORBIDDEN);
        }

        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'message' => '对不起，本条数据已经去了火星~'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof ExcelImportExamineeException) {
            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return $this->invalidJson($request, $exception);
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
