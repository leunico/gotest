<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Factories\Response\ResponseFactory;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Modules\Examinee\Entities\Examinee;
use Grpcexam\ExamRequest;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @return \App\Factories\Response\ResponseFactory
     */
    public function response(): ResponseFactory
    {
        return app(ResponseFactory::class);
    }

    /**
     * @return \App\Models\User
     */
    public function user(): User
    {
        return auth('api')->user();
    }

    /**
     * @return \Modules\Examinee\Entities\Examinee
     */
    public function examinee(): Examinee
    {
        return auth('examinee')->user();
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function respondWithToken($token): JsonResponse
    {
        return $this->response()->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
    
    /*
     * @param \Grpcexam\ExamRequest $gRequest
     * @return \Grpcexam\ExamReply|null
     */
    public function grpcCourse(ExamRequest $gRequest)
    {
        $get = app()->make('grpc')
            ->connection()
            ->sGet($gRequest)
            ->wait();

        list($reply, $status) = $get;
        if (! empty($status->code)) {
            dd($status->details);
            // throw ...
        }

        //数组
        return $reply->getValues();
    }
}
