<?php

namespace Modules\Examinee\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Examinee\Http\Controllers\ExamineeController as Controller;
use Illuminate\Http\JsonResponse;
use Modules\Examination\Entities\Examination;
use Modules\Examination\Entities\ExaminationExaminee;
use Illuminate\Support\Facades\DB;

class AchievementController extends Controller
{
    /**
     * 成绩列表.
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

        $keyword = $request->input('keyword', null);
        $orderRank = $request->input('o_rank', null);
        $orderScore = $request->input('o_score', null);

        $data = $eexaminee->select(
            'examination_examinees.id',
            'examinee_id',
            'examination_id',
            'admission_ticket',
            'is_hand',
            'hand_time',
            'objective_score',
            'subjective_score',
            'rank',
            'name'
        )
            ->leftjoin('examinees', 'examination_examinees.examinee_id', 'examinees.id')
            ->where('examination_id', $examination->id)
            ->where('examination_examinees.status', ExaminationExaminee::STATUS_OK)
            ->when(! is_null($keyword), function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%")
                        ->orWhere('certificates', 'like', "%$keyword%")
                        ->orWhere('admission_ticket', 'like', "%$keyword%")
                        ->orWhere('phone', 'like', "%$keyword%")
                        ->orWhere('email', 'like', "%$keyword%");
                });
            })
            ->when(! is_null($orderRank), function ($query) use ($orderRank) {
                $query->orderBy('rank', empty($orderRank) ? 'asc' : 'desc');
            })
            ->when(! is_null($orderScore), function ($query) use ($orderScore) {
                $query->orderBy(DB::raw('subjective_score + objective_score'), empty($orderScore) ? 'asc' : 'desc');
            })
            ->with([
                'markingRecord' => function ($query) {
                    $query->orderBy('user_id', 'desc')
                        ->orderBy('id', 'desc')
                        ->select('examination_examinee_id', 'id', 'score', 'user_id');
                },
                'markingRecord.user:id,name,real_name'
            ])
            ->paginate($perPage);

        return $this->response()->success($data);
    }

    /**
     * 试卷详情.
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function show(ExaminationExaminee $eexaminee): JsonResponse
    {
        $eexaminee->load([
            'examinee:id,name',
            'examination:id,match_id,title,examination_paper_title,start_at,end_at,examination_category_id',
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
}
