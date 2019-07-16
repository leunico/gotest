<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use App\Traits\FileHandle;

// ...
use Modules\Examinee\Http\Requests\ExamineeAnswerMinecaftRequest;
use Modules\Examinee\Entities\ExamineeAnswer;
use Modules\ExaminationPaper\Entities\Question;
use Modules\Examinee\Transformers\ExamineeAnswerResource;

class ToolController extends Controller
{
    use FileHandle;

    /**
     * 获取服务器时间.
     *
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function time(): JsonResponse
    {
        return $this->response()->success(Carbon::now()->format('Y-m-d H:i:s'));
    }

    /**
     * 获取Mcide跨域内容.
     *
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function crossDomain(): JsonResponse
    {
        return $this->response()->success(file_get_contents(public_path('crossdomain.xml')));
    }

    /**
     * Mc答题部分[因为前端限制放在这里].
     *
     * @param \Modules\Examinee\Http\Requests\ExamineeAnswerMinecaftRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function mcStore(ExamineeAnswerMinecaftRequest $request): JsonResponse
    {
        $answer = ExamineeAnswer::firstOrNew([
            'examinee_id' => $this->examinee()->id,
            'examination_id' => $request->efrom->get('examination_id'),
            'question_id' => $request->efrom->get('question_id')
        ]);

        $answer->answer = $request->efrom->get('answer');
        $answer->answer_time = $request->efrom->get('answer_time');
        $answer->answer_file = $request->scratch[0] ? $this->resetAnswerFile($request->scratch[0], 'scratch/media') : [];
        $answer->type = Question::CATEGORY_OPERATION;
        $answer->save();

        return $this->response()->item($answer, ExamineeAnswerResource::class);
    }
}
