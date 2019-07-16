<?php

namespace Modules\ExaminationPaper\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\ExaminationPaper\Http\Controllers\ExaminationPaperController as Controller;
use Illuminate\Http\JsonResponse;
use Modules\Examination\Entities\ExaminationExaminee;
use Modules\Examination\Entities\Examination;
use Modules\ExaminationPaper\Entities\MarkingRecord;
use Modules\ExaminationPaper\Http\Requests\StoreMarkingRequest;

class MarkingController extends Controller
{
    /**
     * 阅卷列表.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @param \Modules\Examination\Entities\Examination $examination
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, Examination $examination, ExaminationExaminee $eexaminee): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);

        $isHend = $request->input('is_hand', null);
        $status = $request->input('status', null);

        $data = $eexaminee->select('id', 'examinee_id', 'examination_id', 'is_hand', 'hand_time', 'objective_score', 'subjective_score', 'achievement_status')
            ->where('examination_id', $examination->id)
            ->when(! is_null($isHend), function ($query) use ($isHend) {
                $query->where('is_hand', $isHend);
            })
            ->when(! is_null($status), function ($query) use ($status) {
                $query->where('achievement_status', empty($status) ? ExaminationExaminee::ACHIEVEMENT_STATUS_OK : ExaminationExaminee::ACHIEVEMENT_STATUS_MARKING);
            })
            ->with([
                'examinee:id,name',
                'markingRecord' => function ($query) {
                    $query->orderBy('user_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->select('examination_examinee_id', 'id', 'score', 'user_id', 'created_at');
                },
                'markingRecord.user:id,name,real_name'
            ])
            ->paginate($perPage);

        $statistics = ExaminationExaminee::select('id', 'achievement_status')
            ->where('examination_id', $examination->id)
            ->where('is_hand', ExaminationExaminee::IS_HAND_OK)
            ->get();
        return $this->response()->success($data, [
            'normal_total' => $statistics->count(),
            'status_ok' => $statistics->where('achievement_status', ExaminationExaminee::ACHIEVEMENT_STATUS_MARKING)->count()
        ]);
    }

    /**
     * 试卷详情.
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(ExaminationExaminee $eexaminee): JsonResponse
    {
        $eexaminee->load([
            'examinee:id,name',
            'examination:id,match_id,title,examination_paper_title,examination_category_id',
            'examination.majorProblems' => function ($query) {
                $query->orderBy('sort')
                    ->select('id', 'sort', 'title', 'description', 'examination_id', 'total_score', 'is_question_disorder', 'is_option_disorder');
            },
            'examination.majorProblems.questions' => function ($query) {
                $query->orderBy('sort');
            },
            'examination.majorProblems.questions.options' => function ($query) {
                $query->orderBy('sort');
            },
            'examination.majorProblems.questions.examineeAnswer' => function ($query) use ($eexaminee) {
                $query->select('examinee_id', 'id', 'examination_id', 'question_id', 'question_option_id', 'answer', 'answer_time', 'type')
                    ->where('examinee_id', $eexaminee->examinee_id);
            },
            'examination.majorProblems.questions.markingRecord' => function ($query) use ($eexaminee) {
                $query->select('examination_examinee_id', 'id', 'question_id', 'examination_answer_id', 'score', 'user_id')
                    ->where('examination_examinee_id', $eexaminee->id);
            },
        ]);

        return $this->response()->success($eexaminee);
    }

    /**
     * 添加阅卷.
     *
     * @param \Modules\ExaminationPaper\Http\Requests\StoreMarkingRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreMarkingRequest $request): JsonResponse
    {
        $marking = MarkingRecord::firstOrNew([
            'examination_examinee_id' => $request->examination_examinee_id,
            'question_id' => $request->question_id,
        ]);

        $marking->examination_answer_id = $request->input('examination_answer_id', 0);
        $marking->score = $request->score;
        $marking->user_id = $this->user()->id;

        return $marking->save() ? $this->response()->success($marking) : $this->response()->error();
    }

    /**
     * 修改阅卷分数.
     *
     * @param \Modules\ExaminationPaper\Http\Requests\StoreMarkingRequest $request
     * @param \Modules\ExaminationPaper\Entities\MarkingRecord $marking
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreMarkingRequest $request, MarkingRecord $marking): JsonResponse
    {
        $marking->score = $request->score;
        $marking->user_id = $this->user()->id;

        return $marking->save() ? $this->response()->success($marking) : $this->response()->error();
    }
}
