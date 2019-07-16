<?php

namespace Modules\ExaminationPaper\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\ExaminationPaper\Http\Controllers\ExaminationPaperController as Controller;
use Illuminate\Http\JsonResponse;
use Modules\ExaminationPaper\Http\Requests\StoreMajorProblemRequest;
use Modules\ExaminationPaper\Entities\MajorProblem;
use Modules\Examination\Entities\Examination;

class MajorProblemController extends Controller
{
    /**
     * 大题列表.
     *
     * @param \Modules\Examination\Entities\Examination $examination
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Examination $examination): JsonResponse
    {
        $examination->load([
            'majorProblems' => function ($query) {
                $query->select('id', 'examination_id', 'title', 'sort', 'description', 'is_question_disorder', 'is_option_disorder', 'total_score', 'category')
                    ->orderBy('sort');
            },
            'majorProblems.questions' => function ($query) {
                $query->orderBy('sort');
            },
            'majorProblems.questions.options' => function ($query) {
                $query->orderBy('sort');
            },
        ]);

        return $this->response()->success($examination);
    }

    /**
     * 添加大题.
     *
     * @param \Modules\ExaminationPaper\Http\Requests\StoreMajorProblemRequest $request
     * @param \Modules\ExaminationPaper\Entities\MajorProblem $problem
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreMajorProblemRequest $request, MajorProblem $problem): JsonResponse
    {
        $problem->examination_id = $request->examination_id;
        $problem->title = $request->title;
        $problem->description = $request->description;
        $problem->category = $request->category;
        $problem->sort = $request->sort;
        $problem->is_question_disorder = $request->is_question_disorder;
        $problem->is_option_disorder = $request->input('is_option_disorder', 0);

        return $problem->save() ? $this->response()->success($problem) : $this->response()->error();
    }

    /**
     * 修改大题.
     *
     * @param \Modules\ExaminationPaper\Http\Requests\StoreMajorProblemRequest $request
     * @param \Modules\ExaminationPaper\Entities\MajorProblem $problem
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreMajorProblemRequest $request, MajorProblem $problem): JsonResponse
    {
        $problem->title = $request->title;
        $problem->description = $request->description;
        $problem->sort = $request->sort;
        $problem->is_question_disorder = $request->is_question_disorder;
        $problem->is_option_disorder = $request->input('is_option_disorder', 0);

        return $problem->save() ? $this->response()->success($problem) : $this->response()->error();
    }

    /**
     * 获取大题详情.
     *
     * @param \Modules\ExaminationPaper\Entities\MajorProblem $problem
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(MajorProblem $problem): JsonResponse
    {
        $problem->load([
            'questions' => function ($query) {
                $query->orderBy('sort');
            },
            'questions.options' => function ($query) {
                $query->orderBy('sort');
            }
        ]);

        return $this->response()->success($problem);
    }

    /**
     * 设置大题的排序
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\ExaminationPaper\Entities\MajorProblem $problem
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function sort(Request $request, MajorProblem $problem): JsonResponse
    {
        $this->validate($request, ['sorts' => 'required|array']);

        return $problem->batchUpdate($request->sorts) ? $this->response()->success() : $this->response()->error();
    }

    /**
     * 设置大题题目的平均分数
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\ExaminationPaper\Entities\MajorProblem $problem
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function avgScore(Request $request, MajorProblem $problem): JsonResponse
    {
        $this->validate($request, ['score' => 'required|integer']);

        $problem->total_score = $request->score * $problem->questions->count();
        $problem->getConnection()->transaction(function () use ($problem, $request) {
            if ($problem->save()) {
                $problem->questions()->update([
                    'score' => $request->score
                ]);
            }
        });

        return $this->response()->success($problem);
    }

    /**
     * 删除大题.
     *
     * @param \Modules\ExaminationPaper\Entities\MajorProblem $problem
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function destroy(MajorProblem $problem): JsonResponse
    {
        if ($problem->questions->isNotEmpty()) {
            return $this->response()->error('请先删除试题！');
        }

        return $this->response()->success($problem->delete());
    }
}
