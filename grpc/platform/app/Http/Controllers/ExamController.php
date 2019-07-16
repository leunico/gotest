<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\FileHandle;
use GrpcClient\GrpcExamClient;
use Grpcexam\ExamRequest;
use App\FaceUser;
use Illuminate\Support\Carbon;

class ExamController extends Controller
{
    use FileHandle;

    /**
     * store video.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function storeVideo(Request $request): JsonResponse
    {
        $this->validate($request, ['video_url' => 'required']);

        return $this->response()->success();
    }

    public function testGrpc(ExamRequest $gRequest): JsonResponse
    {
        $gRequest->setRouter('SelectCourse');
        $gRequest->setParameters([
            'id' => 1251,
            // 列表
            'status' => 1,
            'perPage' => 15,
            'page' => 2
        ]);

        //调用远程服务
        $getData = $this->grpcCourse($gRequest);

        // dd($getData->offsetGet('other'));
        return $this->response()->success([
            'data' => json_decode($getData->offsetGet('data')),
            // 'page' => json_decode($getData->offsetGet('other'))
        ], true);
    }
}
