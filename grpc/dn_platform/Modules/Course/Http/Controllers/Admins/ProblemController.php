<?php

namespace Modules\Course\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Course\Http\Requests\StoreProblemPost;
use Modules\Course\Entities\Problem;
use Modules\Course\Entities\ProblemDetail;
use Modules\Course\Entities\ProblemOption;
use function App\responseSuccess;
use function App\responseFailed;

class ProblemController extends Controller
{
    /**
     * 题目列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\Problem $problem
     * @param int $tag
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, Problem $problem, int $tag = 1)
    {
        $perPage = (int) $request->input('per_page', 15);

        if (!in_array($tag, [1, 4])) {
            return responseFailed('类型不存在', 404);
        }

        $courseCategory = $request->input('course_category', null);
        $category = $request->input('category', null);
        $keyword = $request->input('keyword', null);

        $data = $problem->select('problems.id', 'problem_details.problem_text', 'category', 'course_category', 'use_count')
            ->leftjoin('problem_details', 'problems.id', 'problem_details.problem_id')
            ->when($tag == 1, function ($query) use ($category, $problem) {
                return $problem->isChoiceQuestionCategory($category) ? $query->where('category', $category) : $query->whereIn('category', Problem::$choice_question_category);
            }, function ($query) use ($tag) {
                return $query->where('category', $tag);
            })
            ->when($courseCategory, function ($query) use ($courseCategory) {
                return $query->where('course_category', $courseCategory);
            })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('problem_text', 'like', "%$keyword%");
            })
            ->orderBy('id')
            ->paginate($perPage);

        return responseSuccess($data);
    }

    /**
     * 添加题目
     *
     * @param \Modules\Course\Http\Requests\StoreProblemPost $request
     * @param \Modules\Course\Entities\Problem $problem
     * @param \Modules\Course\Entities\ProblemDetail $detail
     * @param \Modules\Course\Entities\ProblemOption $option
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreProblemPost $request, Problem $problem, ProblemDetail $detail, ProblemOption $option)
    {
        $problem->category = $request->category;
        $problem->course_category = $request->course_category;
        $problem->preview_id = $request->input('preview_id', null);
        $problem->plan_duration = $request->input('plan_duration', 0);

        $problem->getConnection()->transaction(function () use ($problem, $request, $detail, $option) {
            if ($problem->save()) {
                $detail->answer = $request->input('answer', '');
                $detail->hint = $request->input('hint', '');
                $detail->problem_text = $request->problem_text;

                if ($problem->isChoiceQuestionCategory() && !empty($request->options)) {
                    $items = [];
                    foreach ($request->options as $val) {
                        $option_item = clone $option;
                        $option_item->option_text = empty($val['option_text']) ? '' : $val['option_text'];
                        $option_item->is_true = empty($val['is_true']) ? 0 : $val['is_true'];
                        $option_item->sort = empty($val['sort']) ? 0 : $val['sort'];
                        $items[] = $option_item;
                    }
                    $problem->options()->saveMany($items);
                }
                $problem->detail()->save($detail);
            }
        });

        return responseSuccess([
            'problem_id' => $problem->id
        ], '添加题目成功');
    }

    /**
     * 获取一条题目内容
     *
     * @param \Modules\Course\Entities\Problem $problem
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(Problem $problem)
    {
        $problem->load([
            'preview',
            'detail',
            'options' => function ($query) {
                $query->orderBy('sort')
                    ->select('id', 'problem_id', 'option_text', 'is_true');
            },
        ]);

        return responseSuccess($problem);
    }

    /**
     * 修改题目
     *
     * @param \Modules\Course\Http\Requests\StoreProblemPost $request
     * @param \Modules\Course\Entities\Problem $problem
     * @param \Modules\Course\Entities\ProblemDetail $detail
     * @param \Modules\Course\Entities\ProblemOption $option
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreProblemPost $request, Problem $problem, ProblemDetail $detail, ProblemOption $option)
    {
        $problem->category = $request->category;
        $problem->course_category = $request->course_category;
        $problem->preview_id = $request->input('preview_id', null);
        $problem->plan_duration = $request->input('plan_duration', 0);

        $problem->getConnection()->transaction(function () use ($problem, $request, $detail, $option) {
            if ($problem->save()) {
                $detail = $problem->detail;
                $detail->answer = $request->input('answer', '');
                $detail->hint = $request->input('hint', '');
                $detail->problem_text = $request->problem_text;

                if ($problem->isChoiceQuestionCategory() && !empty($request->options)) {
                    $unset_id = [];
                    foreach ($request->options as $val) {
                        $option_item = empty($val['id']) ? clone $option : ProblemOption::findOrNew($val['id']);
                        $option_item->problem_id = $problem->id;
                        $option_item->option_text = empty($val['option_text']) ? '' : $val['option_text'];
                        $option_item->is_true = empty($val['is_true']) ? 0 : $val['is_true'];
                        $option_item->sort = empty($val['sort']) ? 0 : $val['sort'];
                        $option_item->save();
                        $unset_id[] = $option_item->id;
                    }

                    if ($unset_id) {
                        $problem->options()
                            ->whereNotIn('id', $unset_id)
                            ->delete();
                    }
                }
                $detail->save();
            }
        });

        return responseSuccess([
            'problem_id' => $problem->id
        ], '修改题目成功');
    }

    /**
     * 删除
     *
     * @param \Modules\Course\Entities\Problem $problem
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function destroy(Problem $problem)
    {
        if (! $problem->sectionPivots->isEmpty()) {
            return responseFailed('已有课程使用，不予删除！', 400);
        }

        $problem->delete();

        return responseSuccess();
    }
}
