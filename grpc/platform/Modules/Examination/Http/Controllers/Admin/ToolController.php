<?php

namespace Modules\Examination\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Examination\Http\Controllers\ExaminationController as Controller;
use Illuminate\Http\JsonResponse;
use Modules\Examination\Entities\ExaminationCategory;
use Modules\Examination\Http\Requests\OnlineJudgeRequest;
use Illuminate\Support\Carbon;
use App\Models\Solution;
use App\Models\SourceCode;
use App\Models\CustomInput;
use App\Models\CompileInfo;
use App\Models\RuntimeInfo;
use Modules\Examination\Entities\Examination;

class ToolController extends Controller
{
    /**
     * 考试类型.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function examinationCategroys(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $isAll = (int)$request->input('is_all', 1);
        $isGroup = (int)$request->input('is_group', 1);

        $query = ExaminationCategory::select('id', 'title', 'category');

        return $this->response()->success(
            empty($isAll) ? 
            $query->paginate($perPage) : 
            $query->get()
            ->when($isGroup, function ($query) use ($isGroup) {
                return empty($isGroup) ? $query : $query->groupBy('category');
            })
        );
    }

    /**
     * 考试列表.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function examinations(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $isAll = (int)$request->input('is_all', 1);
        $status = $request->input('status', null);
        $keyword = $request->input('keyword', null);

        $query = Examination::select('id', 'title', 'examination_category_id')
            ->where('status', '>', Examination::STATUS_PAPER)
            ->when(! is_null($keyword), function ($query) use ($keyword) {
                $query->where('examinations.title', 'LIKE', "%$keyword%");
            })
            ->when(! is_null($status), function ($query) use ($status) {
                $query->where('examinations.status', $status);
            });

        return $this->response()-> success(empty($isAll) ? $query->paginate($perPage) : $query->get());
    }

    /**
     * oj提交代码.
     *
     * @param \App\Http\Requests\OnlineJudgeRequest $request
     * @param \App\Models\Solution $solution
     * @param \App\Models\SourceCode $sourceCode
     * @param \App\Models\CustomInput $customInput
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function ojStore(
        OnlineJudgeRequest $request, 
        Solution $solution, 
        SourceCode $sourceCode, 
        CustomInput $customInput): JsonResponse
    {
        $solution->user_id = $this->user()->id;
        $solution->language = $request->language;
        $solution->result = Solution::RESULT_WAITING;
        $solution->in_date = Carbon::now();
        $solution->ip = $request->ip();
        $solution->code_length = strlen($request->source);

        $solution->getConnection()->transaction(function () use ($solution, $sourceCode, $request, $customInput) {
            if ($solution->save()) {
                $sourceCode->source = $request->source;
                $customInput->input_text = $request->input('input_text', '');
                $solution->sourceCode()->save($sourceCode);
                $solution->customInput()->save($customInput);
            }
        });

        return $this->response()->success(['solution_id' => $solution->solution_id]);
    }

    /**
     * oj获取运行结果.
     *
     * @param \App\Models\Solution $solution
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function ojShow(Solution $solution): JsonResponse
    {
        $output = $solution->result == Solution::RESULT_COMPILE_ERR ? 
            CompileInfo::where('solution_id', $solution->solution_id)->first() : 
            RuntimeInfo::where('solution_id', $solution->solution_id)->first();

        return empty($output) ? 
            $this->response()->error() :
            $this->response()->success([
                'output' => $output->error,
                'solution' => $solution
            ]);
    }
}
