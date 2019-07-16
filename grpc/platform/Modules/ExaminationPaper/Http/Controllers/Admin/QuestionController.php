<?php

namespace Modules\ExaminationPaper\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\ExaminationPaper\Http\Controllers\ExaminationPaperController as Controller;
use Modules\ExaminationPaper\Http\Requests\StoreQuestionRequest;
use Modules\ExaminationPaper\Entities\Question;
use Modules\ExaminationPaper\Entities\QuestionOption;
use Illuminate\Http\JsonResponse;
use App\Traits\FileHandle;

class QuestionController extends Controller
{
    use FileHandle;

    /**
     * 添加题目
     *
     * @param \Modules\ExaminationPaper\Http\Requests\StoreQuestionRequest $request
     * @param \Modules\ExaminationPaper\Entities\Question $question
     * @param \Modules\ExaminationPaper\Entities\QuestionOption $option
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreQuestionRequest $request, Question $question, QuestionOption $option): JsonResponse
    {
        $question->major_problem_id = $request->major_problem_id;
        $question->category = $request->category;
        $question->score = $request->score;
        $question->level = $request->level;
        $question->question_title = $request->question_title;
        $question->sort = $request->sort;
        $question->answer = (string) $request->input('answer', '');
        $question->knowledge = $request->input('knowledge', []);
        $question->code = (string) $request->input('code', '');
        $question->code_file = $request->code_file ? $this->handleCodeFile($request->code_file) : [];
        $question->completion_count = $request->input('completion_count', 0);
        $question->getConnection()->transaction(function () use ($question, $request, $option) {
            if ($question->save() && $question->isChoiceQuestionCategory() && ! empty($request->options)) {
                $items = [];
                foreach ($request->options as $val) {
                    $option_item = clone $option;
                    $option_item->option_title = empty($val['option_title']) ? '' : $val['option_title'];
                    $option_item->is_true = empty($val['is_true']) ? 0 : $val['is_true'];
                    $option_item->sort = empty($val['sort']) ? 0 : $val['sort'];
                    $items[] = $option_item;
                }
                $question->options()->saveMany($items);
            }
        });

        return $this->response()->success($question);
    }

    /**
     * 获取一条题目内容
     *
     * @param \Modules\ExaminationPaper\Entities\Question $question
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(Question $question): JsonResponse
    {
        $question->load([
            'options' => function ($query) {
                $query->orderBy('sort')
                    ->select('id', 'question_id', 'option_title', 'is_true', 'sort');
            },
        ]);

        return $this->response()->success($question);
    }

    /**
     * 修改题目
     *
     * @param \Modules\ExaminationPaper\Http\Requests\StoreQuestionRequest $request
     * @param \Modules\ExaminationPaper\Entities\Question $question
     * @param \Modules\ExaminationPaper\Entities\QuestionOption $option
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreQuestionRequest $request, Question $question, QuestionOption $option): JsonResponse
    {
        // $question->category = $request->category; // 类型不给改吧？??
        $question->score = $request->score;
        $question->level = $request->level;
        $question->question_title = $request->question_title;
        $question->sort = $request->sort;
        $question->answer = (string) $request->input('answer', '');
        $question->knowledge = $request->input('knowledge', []);
        $question->code = (string) $request->input('code', '');
        $question->code_file = $request->code_file ? $this->handleCodeFile($request->code_file) : $question->code_file;
        $question->completion_count = $request->input('completion_count', 0);
        $question->getConnection()->transaction(function () use ($question, $request, $option) {
            if ($question->save() && $question->isChoiceQuestionCategory() && !empty($request->options)) {
                $unset_id = [];
                foreach ($request->options as $val) {
                    $option_item = empty($val['id']) ? clone $option : QuestionOption::findOrNew($val['id']);
                    $option_item->question_id = $question->id;
                    $option_item->option_title = empty($val['option_title']) ? '' : $val['option_title'];
                    $option_item->is_true = empty($val['is_true']) ? 0 : $val['is_true'];
                    $option_item->sort = empty($val['sort']) ? 0 : $val['sort'];
                    $option_item->save();
                    $unset_id[] = $option_item->id;
                }

                if ($unset_id) {
                    $question->options()
                        ->whereNotIn('id', $unset_id)
                        ->delete();
                }
            } elseif ($question->isNotChoiceQuestionCategory()) {
                $question->options()->delete();
            }
        });

        return $this->response()->success($question);
    }

    /**
     * 设置题目的排序
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\ExaminationPaper\Entities\Question $question
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function sort(Request $request, Question $question): JsonResponse
    {
        $this->validate($request, ['sorts' => 'required|array']);

        return $question->batchUpdate($request->sorts) ? $this->response()->success() : $this->response()->error();
    }

    /**
     * 删除
     *
     * @param \Modules\ExaminationPaper\Entities\Question $question
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function destroy(Question $question): JsonResponse
    {
        return $this->response()->success($question->delete());
    }
}
