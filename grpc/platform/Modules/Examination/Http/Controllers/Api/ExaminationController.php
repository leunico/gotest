<?php

namespace Modules\Examination\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Examination\Http\Controllers\ExaminationController as Controller;
use Illuminate\Http\JsonResponse;
use Modules\Examination\Entities\Examination;
use Modules\Examination\Transformers\ExaminationRource;
use Modules\Examination\Transformers\ExaminationExamineeRource;
use Modules\Examinee\Jobs\ExamineeQuestionSortJob;
use Modules\Examinee\Entities\ExamineeVideo;
use Modules\Examinee\Entities\ExamineeOperation;

class ExaminationController extends Controller
{
    /**
     * 获取考试内容.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function show(Request $request): JsonResponse
    {
        $examination = $request->examination;
        $examination->load([
            'examFile:id,origin_filename,driver_baseurl,filename',
            'category:id,category,title',
            'majorProblems' => function ($query) {
                $query->orderBy('sort')
                    ->select('id', 'sort', 'title', 'description', 'examination_id', 'total_score', 'is_question_disorder', 'is_option_disorder', 'category');
            },
            'majorProblems.questions',
            'majorProblems.questions.esort' => function ($query) use ($examination) {
                $query->where('examination_examinee_id', $examination->examination_examinee_id)
                    ->select('id', 'examination_examinee_id', 'sorttable_id', 'sort');
            },
            'majorProblems.questions.options',
            'majorProblems.questions.options.esort' => function ($query) use ($examination) {
                $query->where('examination_examinee_id', $examination->examination_examinee_id)
                    ->select('id', 'examination_examinee_id', 'sorttable_id', 'sort');
            },
            'majorProblems.questions.examineeAnswer' => function ($query) {
                $query->select('examinee_id', 'id', 'examination_id', 'question_id', 'question_option_id', 'answer', 'answer_time', 'type', 'answer_file')
                    ->where('examinee_id', $this->examinee()->id);
            },
        ]);

        $examination->majorProblems
            ->map(function ($item) use ($examination) {
                if (! empty($item->is_question_disorder)) {
                    if (empty($item->questions->pluck('esort')->first())) {
                        $item->questions->map(function ($val) {$val->sort = mt_rand(1, 1000);});
                        ExamineeQuestionSortJob::dispatch($examination->examination_examinee_id, $item->questions);
                    }
                }
                if (! empty($item->is_option_disorder)) {
                    $item->questions->map(function ($value) use ($examination){
                        if (empty($value->options->pluck('esort')->first())) {
                            $value->options->map(function ($val) {$val->sort = mt_rand(1, 1000);});
                            ExamineeQuestionSortJob::dispatch($examination->examination_examinee_id, $value->options);
                        }
                    });
                }
            });

        return $this->response()->item($examination, ExaminationRource::class);
    }

    /**
     * 我的考试获取考试内容
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function detail(Request $request): JsonResponse
    {
        $request->eexaminee
            ->load([
                'examineeDeviceProbings:id,examination_examinee_id,is_camera,is_microphone,is_chrome',
                'examineeTencentFaces:id,examination_examinee_id,type,category,sim,result,description',
                'examineeVideos' => function ($query) {
                    $query->select('id', 'examination_examinee_id', 'type')
                        ->where('type', ExamineeVideo::TYPE_BEFORE);
                },
                'examineeOperations' => function ($query) {
                    $query->select('id', 'examination_examinee_id', 'category')
                        ->whereIn('category', [ExamineeOperation::CATEGORY_OFFLINE, ExamineeOperation::CATEGORY_CUTTING_SCREEN]);
                }
            ]);

        return $this->response()->item($request->eexaminee, ExaminationExamineeRource::class);
    }
}
