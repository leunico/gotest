<?php

namespace Modules\ExaminationPaper\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\ExaminationPaper\Http\Controllers\ExaminationPaperController as Controller;
use Illuminate\Http\JsonResponse;
use Modules\ExaminationPaper\Entities\Question;

class QuestionController extends Controller
{
    /**
     * 获取预加载数据.
     *
     * @param \Modules\ExaminationPaper\Entities\Question $question
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function codeJson(Question $question): JsonResponse
    {
        return $this->response()->success($question->code_file ? json_decode(file_get_contents($question->code_file['json_url'] ?? '')) : '');
    }
}
