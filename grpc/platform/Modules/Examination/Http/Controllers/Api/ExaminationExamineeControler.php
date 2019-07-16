<?php

namespace Modules\Examination\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Examination\Http\Controllers\ExaminationController as Controller;
use Modules\Examination\Entities\ExaminationExaminee;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Modules\Examinee\Entities\ExamineeOperation;
use Modules\Examination\Http\Requests\ExamineeOperationRequest;
use Modules\ExaminationPaper\Entities\Question;
use Modules\ExaminationPaper\Transformers\QuestionRource;

class ExaminationExamineeControler extends Controller
{
    /**
     * 设置开始考试时间
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function start(ExaminationExaminee $eexaminee): JsonResponse
    {
        $eexaminee->start_time = $eexaminee->start_time ?? Carbon::now(); // 开考时间是否可以重复设置？

        return $this->response()->success($eexaminee->save());
    }

    /**
     * 添加考生中的操作
     *
     * @param \Modules\Examination\Http\Requests\ExamineeOperationRequest $request
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @param \Modules\Examinee\Entities\ExamineeOperation $operation
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function operation(ExamineeOperationRequest $request, ExamineeOperation $operation, ExaminationExaminee $eexaminee): JsonResponse
    {
        $operation->examination_examinee_id = $eexaminee->id;
        $operation->category = $request->category;
        $operation->remark = $request->input('remark', '');
        $operation->save();
        
        return $this->response()->success([
            'total' => ExamineeOperation::select('id')
                ->where('examination_examinee_id', $eexaminee->id)
                ->where('category', $operation->category)
                ->count()
        ]);
    }

    /**
     * 获取某道考题
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @param \Modules\ExaminationPaper\Entities\Question $question
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function question(ExaminationExaminee $eexaminee, Question $question): JsonResponse
    {
        $question->load([
            'examineeAnswer' => function ($query) {
                $query->select('examinee_id', 'id', 'examination_id', 'question_id', 'question_option_id', 'answer', 'answer_time', 'type', 'answer_file')
                    ->where('examinee_id', $this->examinee()->id);
            }
        ]);
        $question->eexaminee = $eexaminee;

        return $this->response()->item($question, QuestionRource::class);
    }
}
